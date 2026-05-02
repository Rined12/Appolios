<?php
/**
 * APPOLIOS Review Controller
 * Handles review-related business logic
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class ReviewController extends BaseModel {
    protected string $table = 'reviews';
    
    public function getByCourseId($courseId) {
        $sql = "SELECT r.*, u.name as user_name
                FROM {$this->table} r
                JOIN users u ON r.user_id = u.id
                WHERE r.course_id = ?
                ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    public function getByUserId($userId) {
        $sql = "SELECT r.*, c.title as course_title
                FROM {$this->table} r
                JOIN courses c ON r.course_id = c.id
                WHERE r.user_id = ?
                ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (user_id, course_id, rating, comment, created_at)
                VALUES (?, ?, ?, ?, NOW())";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['user_id'],
                $data['course_id'],
                $data['rating'],
                $data['comment'] ?? ''
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function hasUserReviewed($userId, $courseId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    public function getAverageRating($courseId) {
        $sql = "SELECT AVG(rating) as avg, COUNT(*) as count FROM {$this->table} WHERE course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetch();
    }

    public function delete($id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}