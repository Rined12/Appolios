<?php
/**
 * APPOLIOS Enrollment Controller
 * Handles enrollment-related business logic
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class EnrollmentController extends BaseModel {
    protected string $table = 'enrollments';
    
    public function enroll($userId, $courseId) {
        $sql = "INSERT INTO {$this->table} (user_id, course_id, enrolled_at, progress) VALUES (?, ?, NOW(), 0)";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$userId, $courseId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function isEnrolled($userId, $courseId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetch() !== false;
    }

    public function getEnrollment($userId, $courseId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetch();
    }

    public function getUserEnrollments($userId) {
        $sql = "SELECT e.*, c.title, c.description, c.image
                FROM {$this->table} e
                JOIN courses c ON e.course_id = c.id
                WHERE e.user_id = ?
                ORDER BY e.enrolled_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function updateProgress($userId, $courseId, $progress) {
        $sql = "UPDATE {$this->table} SET progress = ? WHERE user_id = ? AND course_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$progress, $userId, $courseId]);
    }

    public function countAll() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function getStudentEnrolledCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function getStudentCompletedCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND progress = 100";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function getStudentInProgressCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND progress > 0 AND progress < 100";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function getStudentAverageProgress($userId) {
        $sql = "SELECT AVG(progress) as avg FROM {$this->table} WHERE user_id = ? AND progress > 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return (float)($result['avg'] ?? 0);
    }

    public function getStudentEnrollmentHistory($userId) {
        $sql = "SELECT 
                    DATE_FORMAT(enrolled_at, '%Y-%m') as month,
                    COUNT(*) as count
                FROM {$this->table}
                WHERE user_id = ? AND enrolled_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(enrolled_at, '%Y-%m')
                ORDER BY month ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getStudentProgressDetails($userId) {
        $sql = "SELECT 
                    c.id,
                    c.title,
                    c.image,
                    e.progress,
                    e.enrolled_at,
                    e.completed_at,
                    (SELECT COUNT(*) FROM lessons l JOIN chapters ch ON l.chapter_id = ch.id WHERE ch.course_id = c.id) as total_lessons
                FROM {$this->table} e
                JOIN courses c ON e.course_id = c.id
                WHERE e.user_id = ?
                ORDER BY e.enrolled_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}