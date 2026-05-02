<?php
/**
 * Review Service
 * Handles course reviews and ratings
 */

require_once __DIR__ . '/../config/database.php';

class ReviewService {
    private $db;
    
    public function __construct() {
        $this->db = getConnection();
    }
    
    public function addReview($userId, $courseId, $rating, $reviewText = null) {
        $sql = "INSERT INTO course_reviews (user_id, course_id, rating, review_text) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE rating = ?, review_text = ?, updated_at = CURRENT_TIMESTAMP";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId, $rating, $reviewText, $rating, $reviewText]);
        
        return true;
    }
    
    public function getCourseReviews($courseId) {
        $sql = "SELECT r.*, u.name as user_name, u.email as user_email
                FROM course_reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.course_id = ? AND r.is_approved = 1
                ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }
    
    public function getCourseRating($courseId) {
        $sql = "SELECT 
                    AVG(rating) as avg_rating, 
                    COUNT(*) as total_reviews,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                FROM course_reviews 
                WHERE course_id = ? AND is_approved = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetch();
    }
    
    public function getUserReview($userId, $courseId) {
        $sql = "SELECT * FROM course_reviews WHERE user_id = ? AND course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetch();
    }
    
    public function getUserReviews($userId) {
        $sql = "SELECT r.*, c.title as course_title
                FROM course_reviews r
                JOIN courses c ON r.course_id = c.id
                WHERE r.user_id = ?
                ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function deleteReview($reviewId, $userId) {
        $sql = "DELETE FROM course_reviews WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$reviewId, $userId]);
        return $stmt->rowCount() > 0;
    }
    
    public function getTopRatedCourses($limit = 10) {
        $sql = "SELECT c.*, 
                    AVG(r.rating) as avg_rating, 
                    COUNT(r.id) as review_count
                FROM courses c
                JOIN course_reviews r ON c.id = r.course_id
                WHERE c.status = 'approved' AND r.is_approved = 1
                GROUP BY c.id
                ORDER BY avg_rating DESC, review_count DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function canReview($userId, $courseId) {
        $sql = "SELECT id FROM enrollments WHERE user_id = ? AND course_id = ? AND progress >= 100";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetch() !== false;
    }
}