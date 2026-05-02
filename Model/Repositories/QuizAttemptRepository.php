<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseRepository.php';

class QuizAttemptRepository extends BaseRepository
{
    private string $table = 'quiz_attempts';

    public function record(int $userId, int $quizId, int $score, int $total, int $percentage): bool
    {
        $sql = "INSERT INTO {$this->table} (user_id, quiz_id, score, total, percentage) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $quizId, $score, $total, $percentage]);
    }

    public function recordAndGetId(int $userId, int $quizId, int $score, int $total, int $percentage): ?int
    {
        $sql = "INSERT INTO {$this->table} (user_id, quiz_id, score, total, percentage) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([$userId, $quizId, $score, $total, $percentage]);
        if (!$ok) {
            return null;
        }
        return (int) $this->db->lastInsertId();
    }

    public function findByIdWithDetails(int $attemptId): ?array
    {
        $attemptId = (int) $attemptId;
        if ($attemptId <= 0) {
            return null;
        }

        $sql = "SELECT a.*, q.title AS quiz_title, u.name AS student_name
                FROM {$this->table} a
                JOIN quizzes q ON q.id = a.quiz_id
                JOIN users u ON u.id = a.user_id
                WHERE a.id = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$attemptId]);
        $row = $stmt->fetch();
        return $row ? $row : null;
    }

    public function getByUserWithQuizTitles(int $userId): array
    {
        $sql = "SELECT a.*, q.title AS quiz_title
                FROM {$this->table} a
                JOIN quizzes q ON q.id = a.quiz_id
                WHERE a.user_id = ?
                ORDER BY a.submitted_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAttemptedQuizIdsByUser(int $userId): array
    {
        $sql = "SELECT DISTINCT quiz_id
                FROM {$this->table}
                WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $userId]);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_map('intval', is_array($ids) ? $ids : []);
    }

    public function getChapterAveragesByUser(int $userId): array
    {
        $sql = "SELECT q.chapter_id, AVG(a.percentage) AS avg_percentage, COUNT(*) AS attempts
                FROM {$this->table} a
                JOIN quizzes q ON q.id = a.quiz_id
                WHERE a.user_id = ?
                GROUP BY q.chapter_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $userId]);
        $rows = $stmt->fetchAll();

        $out = [];
        foreach ($rows as $r) {
            $cid = (int) ($r['chapter_id'] ?? 0);
            if ($cid <= 0) {
                continue;
            }
            $out[$cid] = [
                'avg' => (float) ($r['avg_percentage'] ?? 0),
                'attempts' => (int) ($r['attempts'] ?? 0),
            ];
        }
        return $out;
    }

    public function getRecentPercentagesByChapterForUser(int $userId, int $limitChapters = 30): array
    {
        $limitChapters = max(3, min(200, $limitChapters));

        $rows = [];
        $sql = "SELECT q.chapter_id, a.percentage, a.submitted_at,
                       ROW_NUMBER() OVER (PARTITION BY q.chapter_id ORDER BY a.submitted_at DESC, a.id DESC) AS rn
                FROM {$this->table} a
                JOIN quizzes q ON q.id = a.quiz_id
                WHERE a.user_id = ?";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([(int) $userId]);
            $rows = $stmt->fetchAll();
        } catch (Throwable $e) {
            $rows = [];
        }

        $out = [];

        if (!empty($rows)) {
            foreach ($rows as $r) {
                $cid = (int) ($r['chapter_id'] ?? 0);
                if ($cid <= 0) {
                    continue;
                }
                $rn = (int) ($r['rn'] ?? 0);
                if ($rn < 1 || $rn > 2) {
                    continue;
                }
                if (!isset($out[$cid])) {
                    $out[$cid] = [
                        'last' => null,
                        'prev' => null,
                        'last_at' => null,
                    ];
                }
                $pct = (int) ($r['percentage'] ?? 0);
                if ($rn === 1) {
                    $out[$cid]['last'] = $pct;
                    $out[$cid]['last_at'] = isset($r['submitted_at']) ? (string) $r['submitted_at'] : null;
                } else {
                    $out[$cid]['prev'] = $pct;
                }
                if (count($out) >= $limitChapters) {
                    // still allow filling rn=2 for existing chapters
                }
            }
            return $out;
        }

        $fallbackSql = "SELECT q.chapter_id, a.percentage, a.submitted_at
                        FROM {$this->table} a
                        JOIN quizzes q ON q.id = a.quiz_id
                        WHERE a.user_id = ?
                        ORDER BY a.submitted_at DESC, a.id DESC";
        $stmt = $this->db->prepare($fallbackSql);
        $stmt->execute([(int) $userId]);
        $rows = $stmt->fetchAll();

        $counts = [];
        foreach ($rows as $r) {
            $cid = (int) ($r['chapter_id'] ?? 0);
            if ($cid <= 0) {
                continue;
            }
            $counts[$cid] = (int) ($counts[$cid] ?? 0);
            if ($counts[$cid] >= 2) {
                continue;
            }
            if (!isset($out[$cid])) {
                $out[$cid] = [
                    'last' => null,
                    'prev' => null,
                    'last_at' => null,
                ];
            }
            $pct = (int) ($r['percentage'] ?? 0);
            if ($counts[$cid] === 0) {
                $out[$cid]['last'] = $pct;
                $out[$cid]['last_at'] = isset($r['submitted_at']) ? (string) $r['submitted_at'] : null;
            } else {
                $out[$cid]['prev'] = $pct;
            }
            $counts[$cid]++;
        }

        return $out;
    }

    public function getLastAttemptByChapterInCourse(int $userId, int $courseId): array
    {
        $sql = "SELECT q.chapter_id, MAX(a.submitted_at) AS last_attempt_at, COUNT(*) AS attempts
                FROM {$this->table} a
                JOIN quizzes q ON q.id = a.quiz_id
                JOIN chapters ch ON ch.id = q.chapter_id
                WHERE a.user_id = ? AND ch.course_id = ?
                GROUP BY q.chapter_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $userId, (int) $courseId]);
        $rows = $stmt->fetchAll();

        $out = [];
        foreach ($rows as $r) {
            $cid = (int) ($r['chapter_id'] ?? 0);
            if ($cid <= 0) {
                continue;
            }
            $out[$cid] = [
                'last_attempt_at' => isset($r['last_attempt_at']) ? (string) $r['last_attempt_at'] : null,
                'attempts' => (int) ($r['attempts'] ?? 0),
            ];
        }
        return $out;
    }

    public function getLastPercentagesByUser(int $userId, int $limit = 7): array
    {
        $limit = max(1, min(30, $limit));
        $sql = "SELECT percentage
                FROM {$this->table}
                WHERE user_id = ?
                ORDER BY submitted_at DESC
                LIMIT {$limit}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $vals = array_map('intval', is_array($rows) ? $rows : []);
        $vals = array_reverse($vals);
        return $vals;
    }
}
