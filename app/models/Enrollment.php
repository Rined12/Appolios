<?php
/**
 * APPOLIOS Enrollment Model
 * Handles student course enrollments
 */

require_once __DIR__ . '/../core/Model.php';

class Enrollment extends Model {
    protected $table = 'enrollments';

    /**
     * Enroll student in course
     * @param int $userId
     * @param int $courseId
     * @return bool
     */
    public function enroll($userId, $courseId) {
        $sql = "INSERT INTO {$this->table} (user_id, course_id, enrolled_at, progress) VALUES (?, ?, NOW(), 0)";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$userId, $courseId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Check if user is enrolled
     * @param int $userId
     * @param int $courseId
     * @return bool
     */
    public function isEnrolled($userId, $courseId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetch() !== false;
    }

    /**
     * Get user's enrolled courses
     * @param int $userId
     * @return array
     */
    public function getUserEnrollments($userId) {
        $sql = "SELECT e.*, c.title, c.description, c.video_url
                FROM {$this->table} e
                JOIN courses c ON e.course_id = c.id
                WHERE e.user_id = ?
                ORDER BY e.enrolled_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Update progress
     * @param int $userId
     * @param int $courseId
     * @param int $progress
     * @return bool
     */
    public function updateProgress($userId, $courseId, $progress) {
        $sql = "UPDATE {$this->table} SET progress = ? WHERE user_id = ? AND course_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$progress, $userId, $courseId]);
    }

    /**
     * Count total enrollments
     * @return int
     */
    public function countAll() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['count'];
    }
}