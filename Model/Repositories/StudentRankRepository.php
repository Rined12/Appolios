<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseRepository.php';

class StudentRankRepository extends BaseRepository
{
    private string $table = 'student_ranks';

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
                    rating INT NOT NULL DEFAULT 1000,
                    league VARCHAR(30) NOT NULL DEFAULT 'Bronze',
                    division VARCHAR(10) NOT NULL DEFAULT 'III',
                    last_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY uq_user_rank (user_id),
                    INDEX idx_rating (rating)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        try {
            $this->db->exec($sql);
        } catch (Throwable $e) {
        }
    }

    public function getOrCreate(int $userId): array
    {
        $row = $this->getByUser($userId);
        if ($row !== null) {
            return $row;
        }

        $sql = "INSERT INTO {$this->table} (user_id, rating, league, division) VALUES (?, 1000, 'Bronze', 'III')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $userId]);

        return $this->getByUser($userId) ?? [
            'user_id' => (int) $userId,
            'rating' => 1000,
            'league' => 'Bronze',
            'division' => 'III',
        ];
    }

    public function getByUser(int $userId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateRating(int $userId, int $rating, string $league, string $division): bool
    {
        $sql = "UPDATE {$this->table} SET rating = ?, league = ?, division = ? WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([(int) $rating, (string) $league, (string) $division, (int) $userId]);
    }
}
