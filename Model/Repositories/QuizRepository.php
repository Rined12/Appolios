<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseRepository.php';

class QuizRepository extends BaseRepository
{
    private string $table = 'quizzes';
    private string $linkTable = 'quiz_question_bank';
    private string $attemptsTable = 'quiz_attempts';

    public function __construct(?PDO $db = null)
    {
        parent::__construct($db);
        $this->ensureLinkTable();
        $this->ensureStatusColumn();
    }

    private function ensureStatusColumn(): void
    {
        try {
            $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved'");
        } catch (Throwable $e) {
        }
    }

    public function duplicateForTeacher(int $quizId, int $teacherId)
    {
        $quizId = (int) $quizId;
        $teacherId = (int) $teacherId;
        if ($quizId <= 0 || $teacherId <= 0) {
            return false;
        }

        $sql = "SELECT q.*
                FROM {$this->table} q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                WHERE q.id = ? AND c.created_by = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$quizId, $teacherId]);
        $row = $stmt->fetch();
        if (!$row) {
            return false;
        }

        $inTx = false;
        try {
            $this->db->beginTransaction();
            $inTx = true;

            $title = (string) ($row['title'] ?? 'Quiz');
            $newTitle = trim($title) !== '' ? ($title . ' (Copie)') : 'Quiz (Copie)';
            $chapterId = (int) ($row['chapter_id'] ?? 0);
            $difficulty = (string) ($row['difficulty'] ?? 'beginner');
            $tags = $row['tags'] ?? null;
            $timeLimit = isset($row['time_limit_sec']) ? (int) $row['time_limit_sec'] : null;
            $questionsJson = (string) ($row['questions_json'] ?? '[]');

            $ins = $this->db->prepare(
                "INSERT INTO {$this->table}
                    (chapter_id, title, difficulty, tags, time_limit_sec, questions_json, created_by, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $ok = $ins->execute([
                $chapterId,
                $newTitle,
                $difficulty,
                $tags,
                $timeLimit,
                $questionsJson,
                $teacherId,
                'pending',
            ]);

            if (!$ok) {
                $this->db->rollBack();
                return false;
            }

            $newId = (int) $this->db->lastInsertId();
            if ($newId <= 0) {
                $this->db->rollBack();
                return false;
            }

            if ($this->linkTableExists()) {
                $links = $this->db->prepare("SELECT question_bank_id, sort_order FROM {$this->linkTable} WHERE quiz_id = ? ORDER BY sort_order ASC, id ASC");
                $links->execute([$quizId]);
                $rows = $links->fetchAll();

                if (!empty($rows)) {
                    $insLink = $this->db->prepare("INSERT INTO {$this->linkTable} (quiz_id, question_bank_id, sort_order) VALUES (?, ?, ?)");
                    foreach ($rows as $lr) {
                        $bid = (int) ($lr['question_bank_id'] ?? 0);
                        $so = (int) ($lr['sort_order'] ?? 0);
                        if ($bid <= 0) {
                            continue;
                        }
                        $insLink->execute([$newId, $bid, $so]);
                    }
                }
            }

            $this->db->commit();
            return $newId;
        } catch (Throwable $e) {
            if ($inTx) {
                try { $this->db->rollBack(); } catch (Throwable $e2) {}
            }
            return false;
        }
    }

    private function ensureLinkTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->linkTable} (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    quiz_id INT NOT NULL,
                    question_bank_id INT NOT NULL,
                    sort_order INT NOT NULL DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY uq_quiz_question (quiz_id, question_bank_id),
                    INDEX idx_quiz_sort (quiz_id, sort_order),
                    CONSTRAINT fk_qqb_quiz FOREIGN KEY (quiz_id) REFERENCES {$this->table}(id) ON DELETE CASCADE,
                    CONSTRAINT fk_qqb_bank FOREIGN KEY (question_bank_id) REFERENCES question_bank(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        try {
            $this->db->exec($sql);
        } catch (PDOException $e) {
        }
    }

    private function linkTableExists(): bool
    {
        $sql = "SELECT 1
                FROM information_schema.tables
                WHERE table_schema = DATABASE()
                  AND table_name = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->linkTable]);
        return (bool) $stmt->fetchColumn();
    }

    public static function difficultyLabelFr(string $code): string
    {
        $map = [
            'beginner' => 'Débutant',
            'intermediate' => 'Intermédiaire',
            'advanced' => 'Avancé',
        ];
        return $map[$code] ?? $code;
    }

    public static function normalizeQuestionsFromPost(array $post): array
    {
        $raw = $post['questions'] ?? [];
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $q) {
            if (!is_array($q)) {
                continue;
            }
            $bankId = isset($q['question_bank_id']) ? (int) $q['question_bank_id'] : 0;
            $text = isset($q['question']) ? trim((string) $q['question']) : '';
            $opts = $q['options'] ?? [];
            if (!is_array($opts)) {
                $opts = [];
            }
            $opts = array_values(array_filter(array_map(static function ($o) {
                return trim((string) $o);
            }, $opts)));
            $ca = isset($q['correctAnswer']) ? (int) $q['correctAnswer'] : 0;
            if ($text === '' || count($opts) < 2) {
                continue;
            }
            if ($ca < 0 || $ca >= count($opts)) {
                $ca = 0;
            }
            $item = [
                'question' => $text,
                'options' => $opts,
                'correctAnswer' => $ca,
            ];
            if ($bankId > 0) {
                $item['question_bank_id'] = $bankId;
            }
            $out[] = $item;
        }
        return $out;
    }

    public function getQuizStatsForTeacher(int $teacherId): array
    {
        $sql = "SELECT q.id, q.title, q.difficulty, q.status, q.created_at,
                       ch.title AS chapter_title, c.title AS course_title,
                       COUNT(a.id) AS attempts_count,
                       COALESCE(ROUND(AVG(a.percentage), 1), 0) AS avg_percentage,
                       COALESCE(MAX(a.percentage), 0) AS best_percentage,
                       COALESCE(MAX(a.score), 0) AS best_score,
                       COALESCE(MAX(a.total), 0) AS best_total,
                       MAX(a.submitted_at) AS last_attempt_at
                FROM {$this->table} q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                LEFT JOIN {$this->attemptsTable} a ON a.quiz_id = q.id
                WHERE c.created_by = ?
                GROUP BY q.id
                ORDER BY attempts_count DESC, q.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $teacherId]);
        return $stmt->fetchAll();
    }

    public function getQuizAttemptSeriesMapForTeacher(int $teacherId, int $limitPerQuiz = 120): array
    {
        $limitPerQuiz = max(10, min(300, $limitPerQuiz));
        $sql = "SELECT a.quiz_id, a.submitted_at, a.percentage, a.score, a.total
                FROM {$this->attemptsTable} a
                JOIN {$this->table} q ON q.id = a.quiz_id
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                JOIN (
                    SELECT a2.quiz_id, a2.submitted_at, a2.id,
                           ROW_NUMBER() OVER (PARTITION BY a2.quiz_id ORDER BY a2.submitted_at ASC) AS rn
                    FROM {$this->attemptsTable} a2
                ) x ON x.id = a.id
                WHERE c.created_by = ? AND x.rn <= {$limitPerQuiz}
                ORDER BY a.quiz_id ASC, a.submitted_at ASC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([(int) $teacherId]);
            $rows = $stmt->fetchAll();
        } catch (Throwable $e) {
            $rows = [];
            $quizIdsStmt = $this->db->prepare(
                "SELECT DISTINCT q.id
                 FROM {$this->table} q
                 JOIN chapters ch ON ch.id = q.chapter_id
                 JOIN courses c ON c.id = ch.course_id
                 WHERE c.created_by = ?"
            );
            $quizIdsStmt->execute([(int) $teacherId]);
            $quizIds = $quizIdsStmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($quizIds as $qid) {
                $ser = $this->getQuizAttemptSeriesForAdmin((int) $qid, $limitPerQuiz);
                foreach ($ser as $r) {
                    $r['quiz_id'] = (int) $qid;
                    $rows[] = $r;
                }
            }
        }

        $out = [];
        foreach ($rows as $r) {
            $qid = (int) ($r['quiz_id'] ?? 0);
            if ($qid <= 0) {
                continue;
            }
            if (!isset($out[$qid])) {
                $out[$qid] = [];
            }
            $out[$qid][] = [
                't' => (string) ($r['submitted_at'] ?? ''),
                'p' => (float) ($r['percentage'] ?? 0),
                's' => (int) ($r['score'] ?? 0),
                'tot' => (int) ($r['total'] ?? 0),
            ];
        }
        return $out;
    }

    public function getQuizAttemptSeriesForAdmin(int $quizId, int $limit = 120): array
    {
        $limit = max(10, min(400, $limit));
        $sql = "SELECT a.submitted_at, a.percentage, a.score, a.total
                FROM {$this->attemptsTable} a
                WHERE a.quiz_id = ?
                ORDER BY a.submitted_at ASC
                LIMIT {$limit}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $quizId]);
        return $stmt->fetchAll();
    }

    public function getQuizAttemptSeriesMapForAdmin(int $limitPerQuiz = 120): array
    {
        $limitPerQuiz = max(10, min(300, $limitPerQuiz));
        $sql = "SELECT a.quiz_id, a.submitted_at, a.percentage, a.score, a.total
                FROM {$this->attemptsTable} a
                JOIN (
                    SELECT quiz_id, submitted_at, id,
                           ROW_NUMBER() OVER (PARTITION BY quiz_id ORDER BY submitted_at ASC) AS rn
                    FROM {$this->attemptsTable}
                ) x ON x.id = a.id
                WHERE x.rn <= {$limitPerQuiz}
                ORDER BY a.quiz_id ASC, a.submitted_at ASC";
        try {
            $stmt = $this->db->query($sql);
            $rows = $stmt ? $stmt->fetchAll() : [];
        } catch (Throwable $e) {
            // Fallback for MySQL versions without window functions
            $rows = [];
            $quizIdsStmt = $this->db->query("SELECT DISTINCT quiz_id FROM {$this->attemptsTable}");
            $quizIds = $quizIdsStmt ? $quizIdsStmt->fetchAll(PDO::FETCH_COLUMN) : [];
            foreach ($quizIds as $qid) {
                $ser = $this->getQuizAttemptSeriesForAdmin((int) $qid, $limitPerQuiz);
                foreach ($ser as $r) {
                    $r['quiz_id'] = (int) $qid;
                    $rows[] = $r;
                }
            }
        }

        $out = [];
        foreach ($rows as $r) {
            $qid = (int) ($r['quiz_id'] ?? 0);
            if ($qid <= 0) {
                continue;
            }
            if (!isset($out[$qid])) {
                $out[$qid] = [];
            }
            $out[$qid][] = [
                't' => (string) ($r['submitted_at'] ?? ''),
                'p' => (float) ($r['percentage'] ?? 0),
                's' => (int) ($r['score'] ?? 0),
                'tot' => (int) ($r['total'] ?? 0),
            ];
        }
        return $out;
    }

    public function findWithChapterCourse(int $id): ?array
    {
        $sql = "SELECT q.*, ch.course_id, ch.title AS chapter_title, c.title AS course_title,
                       c.created_by AS course_owner_id
                FROM {$this->table} q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                WHERE q.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $row['questions'] = $this->mergeQuestionsForRow($row);
        return $row;
    }

    public function getApprovedCandidatesForCourse(int $courseId, int $excludeQuizId = 0, int $limit = 30): array
    {
        $limit = max(3, min(200, $limit));
        $sql = "SELECT q.id, q.title, q.difficulty, q.chapter_id, ch.title AS chapter_title, q.created_at
                FROM {$this->table} q
                JOIN chapters ch ON ch.id = q.chapter_id
                WHERE ch.course_id = ?
                  AND (q.status IS NULL OR q.status = 'approved')
                  AND (? = 0 OR q.id <> ?)
                ORDER BY q.created_at DESC
                LIMIT {$limit}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $courseId, (int) $excludeQuizId, (int) $excludeQuizId]);
        return $stmt->fetchAll();
    }

    public function getAllForTeacher(int $teacherId): array
    {
        $sql = "SELECT q.*, ch.title AS chapter_title, ch.id AS chapter_id, c.title AS course_title, c.id AS course_id
                FROM {$this->table} q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                WHERE c.created_by = ?
                ORDER BY q.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacherId]);
        return $this->hydrateQuestionsList($stmt->fetchAll());
    }

    public function getUsageStatsMapForTeacher(int $teacherId): array
    {
        $sql = "SELECT q.id AS quiz_id,
                       COUNT(a.id) AS attempts_count,
                       COALESCE(ROUND(AVG(a.percentage), 1), 0) AS avg_percentage,
                       MAX(a.submitted_at) AS last_attempt_at
                FROM {$this->table} q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                LEFT JOIN {$this->attemptsTable} a ON a.quiz_id = q.id
                WHERE c.created_by = ?
                GROUP BY q.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $teacherId]);
        $rows = $stmt->fetchAll();

        $out = [];
        foreach ($rows as $r) {
            $id = (int) ($r['quiz_id'] ?? 0);
            if ($id <= 0) {
                continue;
            }
            $out[$id] = [
                'attempts' => (int) ($r['attempts_count'] ?? 0),
                'avg' => (float) ($r['avg_percentage'] ?? 0),
                'last_attempt_at' => isset($r['last_attempt_at']) ? (string) $r['last_attempt_at'] : null,
            ];
        }
        return $out;
    }

    public function getAllForAdmin(): array
    {
        $sql = "SELECT q.*, ch.title AS chapter_title, c.title AS course_title, c.id AS course_id
                FROM {$this->table} q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                ORDER BY q.created_at DESC";
        $stmt = $this->db->query($sql);
        return $this->hydrateQuestionsList($stmt ? $stmt->fetchAll() : []);
    }

    public function getQuizHistoryForAdmin(): array
    {
        $sql = "SELECT q.*, ch.title AS chapter_title, c.title AS course_title, c.id AS course_id,
                       u.name AS author_name, u.role AS author_role
                FROM {$this->table} q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                LEFT JOIN users u ON u.id = q.created_by
                ORDER BY q.created_at DESC";
        $stmt = $this->db->query($sql);
        return $this->hydrateQuestionsList($stmt ? $stmt->fetchAll() : []);
    }

    public function getQuizStatsForAdmin(): array
    {
        $sql = "SELECT q.id, q.title, q.difficulty, q.status, q.created_at,
                       ch.title AS chapter_title, c.title AS course_title,
                       u.name AS author_name,
                       COUNT(a.id) AS attempts_count,
                       COALESCE(ROUND(AVG(a.percentage), 1), 0) AS avg_percentage,
                       COALESCE(MAX(a.percentage), 0) AS best_percentage,
                       COALESCE(MAX(a.score), 0) AS best_score,
                       COALESCE(MAX(a.total), 0) AS best_total,
                       MAX(a.submitted_at) AS last_attempt_at
                FROM {$this->table} q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                LEFT JOIN users u ON u.id = q.created_by
                LEFT JOIN {$this->attemptsTable} a ON a.quiz_id = q.id
                GROUP BY q.id
                ORDER BY attempts_count DESC, q.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function getQuizAttemptsTrendForAdmin(int $days = 21): array
    {
        $days = max(7, min(90, $days));
        $sql = "SELECT a.quiz_id,
                       DATE(a.submitted_at) AS day,
                       ROUND(AVG(a.percentage), 1) AS avg_percentage,
                       COUNT(*) AS attempts_count
                FROM {$this->attemptsTable} a
                WHERE a.submitted_at >= DATE_SUB(CURDATE(), INTERVAL {$days} DAY)
                GROUP BY a.quiz_id, DATE(a.submitted_at)
                ORDER BY day ASC";
        $stmt = $this->db->query($sql);
        $rows = $stmt ? $stmt->fetchAll() : [];

        $out = [];
        foreach ($rows as $r) {
            $qid = (int) ($r['quiz_id'] ?? 0);
            if ($qid <= 0) {
                continue;
            }
            if (!isset($out[$qid])) {
                $out[$qid] = [];
            }
            $out[$qid][] = [
                'day' => (string) ($r['day'] ?? ''),
                'avg' => (float) ($r['avg_percentage'] ?? 0),
                'count' => (int) ($r['attempts_count'] ?? 0),
            ];
        }
        return $out;
    }

    public function setStatus(int $quizId, string $status): bool
    {
        $allowed = ['pending', 'approved', 'rejected'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $quizId]);
    }

    public function getForEnrolledStudent(int $userId): array
    {
        $sql = "SELECT q.id, q.title, q.difficulty, q.tags, q.time_limit_sec, q.chapter_id,
                       ch.title AS chapter_title, c.id AS course_id, c.title AS course_title
                FROM {$this->table} q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                JOIN enrollments e ON e.course_id = c.id AND e.user_id = ?
                WHERE (q.status IS NULL OR q.status = 'approved')
                ORDER BY c.title ASC, ch.sort_order ASC, q.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll();
        if ($rows !== []) {
            $counts = $this->getQuestionCountsForQuizIds(array_column($rows, 'id'));
            foreach ($rows as &$row) {
                $row['question_count'] = $counts[(int) $row['id']] ?? 0;
            }
            unset($row);
        }
        return $rows;
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO {$this->table}
            (chapter_id, title, difficulty, tags, time_limit_sec, questions_json, created_by, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $tags = array_key_exists('tags', $data) ? (string) $data['tags'] : null;
        $jsonPayload = $this->questionsJsonForStorage($data['questions'] ?? []);
        $json = json_encode($jsonPayload, JSON_UNESCAPED_UNICODE);

        $inTx = false;
        try {
            $inTx = $this->db->beginTransaction();
            $ok = $stmt->execute([
                (int) ($data['chapter_id'] ?? 0),
                $data['title'] ?? '',
                $data['difficulty'] ?? 'beginner',
                $tags,
                $data['time_limit_sec'] !== null && $data['time_limit_sec'] !== '' ? (int) $data['time_limit_sec'] : null,
                $json,
                (int) ($data['created_by'] ?? 0),
                $data['status'] ?? 'approved',
            ]);
            if (!$ok) {
                if ($inTx) {
                    $this->db->rollBack();
                }
                return false;
            }
            $newId = (int) $this->db->lastInsertId();
            if ($newId <= 0) {
                if ($inTx) {
                    $this->db->rollBack();
                }
                return false;
            }
            $this->syncQuestionBankLinks($newId, $data['questions'] ?? []);
            if ($inTx) {
                $this->db->commit();
            }
            return $newId;
        } catch (Throwable $e) {
            if ($inTx && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function update(int $id, array $data): bool
    {
        $hasStatus = array_key_exists('status', $data);
        $sql = "UPDATE {$this->table} SET chapter_id = ?, title = ?, difficulty = ?, tags = ?, time_limit_sec = ?, questions_json = ?";
        if ($hasStatus) {
            $sql .= ", status = ?";
        }
        $sql .= " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $tags = array_key_exists('tags', $data) ? (string) $data['tags'] : null;
        $jsonPayload = $this->questionsJsonForStorage($data['questions'] ?? []);
        $json = json_encode($jsonPayload, JSON_UNESCAPED_UNICODE);

        $inTx = false;
        try {
            $inTx = $this->db->beginTransaction();
            $params = [
                (int) ($data['chapter_id'] ?? 0),
                $data['title'] ?? '',
                $data['difficulty'] ?? 'beginner',
                $tags,
                $data['time_limit_sec'] !== null && $data['time_limit_sec'] !== '' ? (int) $data['time_limit_sec'] : null,
                $json,
            ];
            if ($hasStatus) {
                $params[] = (string) ($data['status'] ?? 'approved');
            }
            $params[] = $id;
            $ok = $stmt->execute($params);
            if (!$ok) {
                if ($inTx) {
                    $this->db->rollBack();
                }
                return false;
            }
            $this->syncQuestionBankLinks($id, $data['questions'] ?? []);
            if ($inTx) {
                $this->db->commit();
            }
            return true;
        } catch (Throwable $e) {
            if ($inTx && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getQuestionCountsForQuizIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($ids === []) {
            return [];
        }
        $ph = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT id, questions_json FROM {$this->table} WHERE id IN ($ph)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($ids);
        $out = [];
        while ($row = $stmt->fetch()) {
            $d = json_decode($row['questions_json'] ?? '[]', true);
            $manual = 0;
            if (is_array($d)) {
                foreach ($d as $item) {
                    if (!is_array($item)) {
                        continue;
                    }
                    if (!empty($item['question_bank_id'])) {
                        continue;
                    }
                    $manual++;
                }
            }
            $out[(int) $row['id']] = $manual;
        }

        if ($this->linkTableExists()) {
            $ph2 = implode(',', array_fill(0, count($ids), '?'));
            $sql2 = "SELECT quiz_id, COUNT(*) AS c FROM {$this->linkTable} WHERE quiz_id IN ($ph2) GROUP BY quiz_id";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute($ids);
            while ($r = $stmt2->fetch()) {
                $qid = (int) ($r['quiz_id'] ?? 0);
                $add = (int) ($r['c'] ?? 0);
                if ($qid > 0) {
                    $out[$qid] = ($out[$qid] ?? 0) + $add;
                }
            }
        }

        return $out;
    }

    private function questionsJsonForStorage(array $questions): array
    {
        $out = [];
        foreach ($questions as $q) {
            if (!is_array($q)) {
                continue;
            }
            $text = trim((string) ($q['question'] ?? ''));
            $opts = $q['options'] ?? [];
            if (!is_array($opts)) {
                $opts = [];
            }
            $opts = array_values(array_filter(array_map(static function ($o) {
                return trim((string) $o);
            }, $opts)));
            $ca = isset($q['correctAnswer']) ? (int) $q['correctAnswer'] : 0;
            if ($text === '' || count($opts) < 2) {
                continue;
            }
            if ($ca < 0 || $ca >= count($opts)) {
                $ca = 0;
            }
            $item = [
                'question' => $text,
                'options' => $opts,
                'correctAnswer' => $ca,
            ];
            if (!empty($q['question_bank_id'])) {
                $item['question_bank_id'] = (int) $q['question_bank_id'];
            }
            $out[] = $item;
        }
        return $out;
    }

    private function hydrateQuestionsList(array $rows): array
    {
        foreach ($rows as &$row) {
            $row['questions'] = $this->mergeQuestionsForRow($row);
        }
        unset($row);
        return $rows;
    }

    private function mergeQuestionsForRow(array $row): array
    {
        $manual = [];
        if (!empty($row['questions_json'])) {
            $d = json_decode($row['questions_json'], true);
            $manual = is_array($d) ? $d : [];
        }

        $quizId = (int) ($row['id'] ?? 0);
        $fromBank = $quizId > 0 ? $this->fetchLinkedBankQuestions($quizId) : [];

        if ($fromBank === []) {
            return $manual;
        }

        $bankIds = [];
        foreach ($fromBank as $bq) {
            $bid = (int) ($bq['question_bank_id'] ?? 0);
            if ($bid > 0) {
                $bankIds[$bid] = true;
            }
        }

        $filteredManual = [];
        foreach ($manual as $m) {
            if (!is_array($m)) {
                continue;
            }
            $mid = isset($m['question_bank_id']) ? (int) $m['question_bank_id'] : 0;
            if ($mid > 0 && isset($bankIds[$mid])) {
                continue;
            }
            $filteredManual[] = $m;
        }

        return array_merge($filteredManual, $fromBank);
    }

    private function fetchLinkedBankQuestions(int $quizId): array
    {
        if (!$this->linkTableExists()) {
            return [];
        }

        $sql = "SELECT l.question_bank_id, l.sort_order,
                       qb.question_text, qb.options_json, qb.correct_answer
                FROM {$this->linkTable} l
                JOIN question_bank qb ON qb.id = l.question_bank_id
                WHERE l.quiz_id = ?
                ORDER BY l.sort_order ASC, l.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$quizId]);
        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $opts = json_decode($r['options_json'] ?? '[]', true);
            if (!is_array($opts)) {
                $opts = [];
            }
            $ca = (int) ($r['correct_answer'] ?? 0);
            if ($ca < 0 || $ca >= count($opts)) {
                $ca = 0;
            }
            $out[] = [
                'question_bank_id' => (int) ($r['question_bank_id'] ?? 0),
                'question' => trim((string) ($r['question_text'] ?? '')),
                'options' => array_values($opts),
                'correctAnswer' => $ca,
            ];
        }
        return $out;
    }

    private function syncQuestionBankLinks(int $quizId, array $questions): void
    {
        if (!$this->linkTableExists() || $quizId <= 0) {
            return;
        }

        $del = $this->db->prepare("DELETE FROM {$this->linkTable} WHERE quiz_id = ?");
        $del->execute([$quizId]);

        $ins = $this->db->prepare("INSERT INTO {$this->linkTable} (quiz_id, question_bank_id, sort_order) VALUES (?, ?, ?)");
        $pos = 0;
        foreach ($questions as $q) {
            if (!is_array($q)) {
                continue;
            }
            $bid = isset($q['question_bank_id']) ? (int) $q['question_bank_id'] : 0;
            if ($bid <= 0) {
                continue;
            }
            $ins->execute([$quizId, $bid, $pos]);
            $pos++;
        }
    }
}
