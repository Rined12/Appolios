<?php

require_once __DIR__ . '/../core/Model.php';

class QuizAttempt extends Model {
    protected $table = 'quiz_attempts';

    public function record($userId, $quizId, $score, $total, $percentage) {
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

    public function getByUserWithQuizTitles($userId) {
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
