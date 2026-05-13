<?php
require_once __DIR__ . '/../Model/BaseModel.php';

class CourseReviewController extends BaseModel {
    protected string $table = 'course_reviews';

    public function hasUserReviewed($userId, $courseId) {
        $sql = "SELECT id FROM {$this->table} WHERE user_id = ? AND course_id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetch() !== false;
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (user_id, course_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['user_id'],
            $data['course_id'],
            $data['rating'],
            $data['comment'] ?? ''
        ]);
    }

    public function getByCourseId($courseId) {
        $sql = "SELECT r.*, u.name as user_name FROM {$this->table} r JOIN users u ON r.user_id = u.id WHERE course_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    public function getAverageRating($courseId) {
        $sql = "SELECT AVG(rating) as avg FROM {$this->table} WHERE course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return round($stmt->fetch()['avg'] ?? 0, 1);
    }
}
