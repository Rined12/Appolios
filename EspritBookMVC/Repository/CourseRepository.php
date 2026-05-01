<?php
/**
 * APPOLIOS Course Model
 * Handles course-related database operations
 */

require_once __DIR__ . '/BaseRepository.php';

class CourseRepository extends BaseRepository {
    protected string $table = 'courses';

    
    /**
     * Get all courses with creator info
     * @return array
     */
    public function getAllWithCreator() {
        $sql = "SELECT c.*, u.name as creator_name
                FROM {$this->table} c
                JOIN users u ON c.created_by = u.id
                ORDER BY c.created_at DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Create a new course
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (title, description, video_url, created_by, created_at)
                VALUES (?, ?, ?, ?, NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['title'],
                $data['description'],
                $data['video_url'],
                $data['created_by']
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update course
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET title = ?, description = ?, video_url = ? WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['video_url'],
            $id
        ]);
    }

    /**
     * Get courses by creator
     * @param int $userId
     * @return array
     */
    public function getByCreator($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE created_by = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get course with creator info
     * @param int $id
     * @return array|null
     */
    public function getWithCreator($id) {
        $sql = "SELECT c.*, u.name as creator_name
                FROM {$this->table} c
                JOIN users u ON c.created_by = u.id
                WHERE c.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get courses by teacher (alias for getByCreator)
     * @param int $teacherId
     * @return array
     */
    public function getCoursesByTeacher($teacherId) {
        return $this->getByCreator($teacherId);
    }

    /**
     * Count unique students enrolled in teacher's courses
     * @param int $teacherId
     * @return int
     */
    public function countStudentsByTeacher($teacherId) {
        $sql = "SELECT COUNT(DISTINCT e.user_id) as count 
                FROM enrollments e 
                JOIN courses c ON e.course_id = c.id 
                WHERE c.created_by = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacherId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Count active enrollments for teacher's courses
     * @param int $teacherId
     * @return int
     */
    public function countActiveEnrollmentsByTeacher($teacherId) {
        $sql = "SELECT COUNT(*) as count 
                FROM enrollments e 
                JOIN courses c ON e.course_id = c.id 
                WHERE c.created_by = ? AND e.progress < 100";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacherId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Get enrolled students for a course
     * @param int $courseId
     * @return array
     */
    public function getEnrolledStudents($courseId) {
        $sql = "SELECT u.id, u.name, u.email, e.progress, e.enrolled_at
                FROM enrollments e
                JOIN users u ON e.user_id = u.id
                WHERE e.course_id = ?
                ORDER BY e.enrolled_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    /**
     * Find course by ID
     * @param int $id
     * @return array|null
     */
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Delete course
     * @param int $id
     * @return bool
     */
    public function delete($id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}