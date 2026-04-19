<?php

require_once __DIR__ . '/../Model/BaseModel.php';

class QuizAttempt extends BaseModel
{
    protected string $table = 'quiz_attempts';

    public function record($userId, $quizId, $score, $total, $percentage): bool
    {
        $sql = "INSERT INTO {$this->table} (user_id, quiz_id, score, total, percentage) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            (int) $userId,
            (int) $quizId,
            (int) $score,
            (int) $total,
            (int) $percentage,
        ]);
    }

    public function getByUserWithQuizTitles($userId): array
    {
        $sql = "SELECT a.*, q.title AS quiz_title
                FROM {$this->table} a
                JOIN quizzes q ON q.id = a.quiz_id
                WHERE a.user_id = ?
                ORDER BY a.submitted_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $userId]);
        return $stmt->fetchAll();
    }
}

