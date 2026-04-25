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
}
