<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseRepository.php';

class StudentQuizFlagRepository extends BaseRepository
{
    private string $table = 'student_quiz_flags';

    public function __construct(?PDO $db = null)
    {
        parent::__construct($db);
        $this->ensureTable();
    }

    private function ensureTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    quiz_id INT NOT NULL,
                    is_favorite TINYINT(1) NOT NULL DEFAULT 0,
                    is_redo TINYINT(1) NOT NULL DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY uq_user_quiz (user_id, quiz_id),
                    INDEX idx_user_fav (user_id, is_favorite),
                    INDEX idx_user_redo (user_id, is_redo)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        try {
            $this->db->exec($sql);
        } catch (Throwable $e) {
        }
    }

    public function getFlagsMapByUser(int $userId): array
    {
        $sql = "SELECT quiz_id, is_favorite, is_redo FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $userId]);
        $rows = $stmt->fetchAll();

        $out = [];
        foreach ($rows as $r) {
            $qid = (int) ($r['quiz_id'] ?? 0);
            if ($qid <= 0) {
                continue;
            }
            $out[$qid] = [
                'favorite' => !empty($r['is_favorite']),
                'redo' => !empty($r['is_redo']),
            ];
        }
        return $out;
    }

    public function getFavoriteQuizIds(int $userId): array
    {
        $sql = "SELECT quiz_id FROM {$this->table} WHERE user_id = ? AND is_favorite = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $userId]);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_map('intval', is_array($ids) ? $ids : []);
    }

    public function getRedoQuizIds(int $userId): array
    {
        $sql = "SELECT quiz_id FROM {$this->table} WHERE user_id = ? AND is_redo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $userId]);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_map('intval', is_array($ids) ? $ids : []);
    }

    public function toggleFavorite(int $userId, int $quizId): bool
    {
        $sql = "INSERT INTO {$this->table} (user_id, quiz_id, is_favorite)
                VALUES (?, ?, 1)
                ON DUPLICATE KEY UPDATE is_favorite = 1 - is_favorite";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([(int) $userId, (int) $quizId]);
    }

    public function toggleRedo(int $userId, int $quizId): bool
    {
        $sql = "INSERT INTO {$this->table} (user_id, quiz_id, is_redo)
                VALUES (?, ?, 1)
                ON DUPLICATE KEY UPDATE is_redo = 1 - is_redo";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([(int) $userId, (int) $quizId]);
    }
}
