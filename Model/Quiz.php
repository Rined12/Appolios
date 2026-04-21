<?php

require_once __DIR__ . '/../Model/BaseModel.php';

class Quiz extends BaseModel
{
    protected string $table = 'quizzes';

    /** Table de liaison quiz ↔ banque (jointure SQL, en plus du JSON historique). */
    private string $linkTable = 'quiz_question_bank';

    public function __construct()
    {
        parent::__construct();
        $this->ensureLinkTable();
    }

    public static function difficultyLabelFr($code): string
    {
        $map = [
            'beginner' => 'Débutant',
            'intermediate' => 'Intermédiaire',
            'advanced' => 'Avancé',
        ];
        $c = (string) $code;
        return $map[$c] ?? $c;
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

    public function findWithChapterCourse($id)
    {
        $sql = "SELECT q.*, ch.course_id, ch.title AS chapter_title, c.title AS course_title,
                       c.created_by AS course_owner_id
                FROM {$this->table} q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                WHERE q.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $id]);
        $row = $stmt->fetch();
        if ($row) {
            $row['questions'] = $this->mergeQuestionsForRow($row);
        }
        return $row;
    }

    public function getAllForTeacher($teacherId): array
    {
        $sql = "SELECT q.*, ch.title AS chapter_title, ch.id AS chapter_id, c.title AS course_title, c.id AS course_id
                FROM {$this->table} q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                WHERE c.created_by = ?
                ORDER BY q.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $teacherId]);
        return $this->hydrateQuestionsList($stmt->fetchAll());
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

    public function getByChapterId($chapterId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE chapter_id = ? ORDER BY id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $chapterId]);
        return $this->hydrateQuestionsList($stmt->fetchAll());
    }

    public function getForEnrolledStudent($userId): array
    {
        $sql = "SELECT q.id, q.title, q.difficulty, q.tags, q.time_limit_sec, q.chapter_id,
                       ch.title AS chapter_title, c.id AS course_id, c.title AS course_title
                FROM {$this->table} q
                JOIN chapters ch ON ch.id = q.chapter_id
                JOIN courses c ON c.id = ch.course_id
                JOIN enrollments e ON e.course_id = c.id AND e.user_id = ?
                ORDER BY c.title ASC, ch.sort_order ASC, q.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $userId]);
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

    public function getChaptersForEnrolledStudent($userId): array
    {
        $sql = "SELECT ch.*, c.title AS course_title, c.id AS course_id
                FROM chapters ch
                JOIN courses c ON c.id = ch.course_id
                JOIN enrollments e ON e.course_id = c.id AND e.user_id = ?
                ORDER BY c.title ASC, ch.sort_order ASC, ch.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $userId]);
        return $stmt->fetchAll();
    }

    private function hydrateQuestionsList(array $rows): array
    {
        foreach ($rows as &$row) {
            $row['questions'] = $this->mergeQuestionsForRow($row);
        }
        unset($row);
        return $rows;
    }

    /**
     * Fusionne les questions JSON (manuelles / snapshot) et les questions liées à la banque (jointure).
     *
     * @param array $row Ligne de la table quizzes (avec questions_json).
     * @return array<int, array<string, mixed>>
     */
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

    /**
     * @return array<int, array<string, mixed>>
     */
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
            // Dégradation silencieuse : sans cette table, le quiz reste fonctionnel via questions_json.
        }
    }

    /**
     * @param array<int, array<string, mixed>> $questions
     */
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

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table}
            (chapter_id, title, difficulty, tags, time_limit_sec, questions_json, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
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

    public function update($id, $data): bool
    {
        $sql = "UPDATE {$this->table} SET chapter_id = ?, title = ?, difficulty = ?, tags = ?, time_limit_sec = ?, questions_json = ?
                WHERE id = ?";
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
                (int) $id,
            ]);
            if (!$ok) {
                if ($inTx) {
                    $this->db->rollBack();
                }
                return false;
            }
            $this->syncQuestionBankLinks((int) $id, $data['questions'] ?? []);
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

    /**
     * Stocke en JSON surtout les questions « manuelles » ; les entrées banque sont portées par la table de liaison.
     *
     * @param array<int, array<string, mixed>> $questions
     * @return array<int, array<string, mixed>>
     */
    private function questionsJsonForStorage(array $questions): array
    {
        $out = [];
        foreach ($questions as $q) {
            if (!is_array($q)) {
                continue;
            }
            if (!empty($q['question_bank_id'])) {
                continue;
            }
            $out[] = $q;
        }
        return $out;
    }
}

