<?php
/**
 * APPOLIOS Course Controller
 * Handles course-related business logic
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class CourseController extends BaseModel {
    protected string $table = 'courses';
    
    /**
     * Get all courses with creator info
     * @return array
     */
    public function getAllWithCreator() {
        $sql = "SELECT c.*, u.name as creator_name, c.price
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
        $sql = "INSERT INTO {$this->table} (title, description, image, course_type, category_id, status, price, admin_message, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['title'],
                $data['description'],
                $data['image'] ?? null,
                $data['course_type'] ?? null,
                $data['category_id'] ?? null,
                $data['status'] ?? 'pending',
                $data['price'] ?? 0.0,
                $data['admin_message'] ?? null,
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
        $sql = "UPDATE {$this->table} SET title = ?, description = ?, image = ?, course_type = ?, category_id = ?, status = ?, price = ?, admin_message = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['image'] ?? null,
            $data['course_type'] ?? null,
            $data['category_id'] ?? null,
            $data['status'] ?? 'pending',
            $data['price'] ?? 0.0,
            $data['admin_message'] ?? null,
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
     * Get courses by teacher
     * @param int $teacherId
     * @return array
     */
    public function getCoursesByTeacher($teacherId) {
        $sql = "SELECT c.*,
                (SELECT COUNT(*) FROM chapters ch WHERE ch.course_id = c.id) as chapter_count,
                (SELECT COUNT(*) FROM lessons l JOIN chapters ch ON l.chapter_id = ch.id WHERE ch.course_id = c.id) as lesson_count
                FROM {$this->table} c 
                WHERE c.created_by = ?
                ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll();
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
    
    /**
     * Search courses by title or description
     * @param string $searchTerm
     * @return array
     */
    public function searchCourses($searchTerm) {
        $sql = "SELECT c.*, u.name as creator_name,
                (SELECT COUNT(*) FROM chapters ch WHERE ch.course_id = c.id) as chapters_count,
                (SELECT COUNT(*) FROM lessons l JOIN chapters ch ON l.chapter_id = ch.id WHERE ch.course_id = c.id) as lessons_count
                FROM {$this->table} c
                JOIN users u ON c.created_by = u.id
                WHERE c.status = 'approved' AND (c.title LIKE ? OR c.description LIKE ?)
                ORDER BY c.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $searchPattern = "%{$searchTerm}%";
        $stmt->execute([$searchPattern, $searchPattern]);
        return $stmt->fetchAll();
    }
    
    /**
     * Filter courses by category and price range
     * @param int|null $categoryId
     * @param float|null $minPrice
     * @param float|null $maxPrice
     * @return array
     */
    public function filterCourses($categoryId = null, $minPrice = null, $maxPrice = null) {
        $sql = "SELECT c.*, u.name as creator_name,
                (SELECT COUNT(*) FROM chapters ch WHERE ch.course_id = c.id) as chapters_count,
                (SELECT COUNT(*) FROM lessons l JOIN chapters ch ON l.chapter_id = ch.id WHERE ch.course_id = c.id) as lessons_count
                FROM {$this->table} c
                JOIN users u ON c.created_by = u.id
                WHERE c.status = 'approved'";
        $params = [];
        
        if ($categoryId) {
            $sql .= " AND c.category_id = ?";
            $params[] = $categoryId;
        }
        if ($minPrice !== null) {
            $sql .= " AND c.price >= ?";
            $params[] = $minPrice;
        }
        if ($maxPrice !== null) {
            $sql .= " AND c.price <= ?";
            $params[] = $maxPrice;
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get courses by price range
     * @param bool $free
     * @return array
     */
    public function getCoursesByPrice($free = true) {
        $sql = "SELECT c.*, u.name as creator_name FROM {$this->table} c
                JOIN users u ON c.created_by = u.id
                WHERE c.status = 'approved'";
        
        if ($free) {
            $sql .= " AND c.price = 0";
        } else {
            $sql .= " AND c.price > 0";
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get teacher's total courses count
     * @param int $teacherId
     * @return int
     */
    public function getTeacherTotalCourses($teacherId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE created_by = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacherId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    /**
     * Get teacher's published courses count
     * @param int $teacherId
     * @return int
     */
    public function getTeacherPublishedCourses($teacherId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE created_by = ? AND status = 'approved'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacherId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    /**
     * Get teacher's pending courses count
     * @param int $teacherId
     * @return int
     */
    public function getTeacherPendingCourses($teacherId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE created_by = ? AND status = 'pending'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacherId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    /**
     * Get teacher's total students enrolled
     * @param int $teacherId
     * @return int
     */
    public function getTeacherTotalStudents($teacherId) {
        $sql = "SELECT COUNT(DISTINCT e.user_id) as count 
                FROM enrollments e 
                JOIN {$this->table} c ON e.course_id = c.id 
                WHERE c.created_by = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacherId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    /**
     * Get teacher's total earnings
     * @param int $teacherId
     * @return float
     */
    public function getTeacherTotalEarnings($teacherId) {
        $sql = "SELECT COALESCE(SUM(c.price), 0) as total 
                FROM enrollments e 
                JOIN {$this->table} c ON e.course_id = c.id 
                WHERE c.created_by = ? AND c.price > 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacherId]);
        $result = $stmt->fetch();
        return (float)($result['total'] ?? 0);
    }
    
    /**
     * Get teacher's course performance
     * @param int $teacherId
     * @return array
     */
    public function getTeacherCoursePerformance($teacherId) {
        $sql = "SELECT 
                    c.id, c.title, c.status, c.price, c.created_at,
                    (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as students,
                    (SELECT COALESCE(SUM(c.price), 0) FROM enrollments WHERE course_id = c.id) as earnings,
                    (SELECT AVG(r.rating) FROM course_reviews r WHERE r.course_id = c.id) as avg_rating,
                    (SELECT AVG(progress) FROM enrollments WHERE course_id = c.id) as avg_progress,
                    (SELECT COUNT(*) FROM chapters WHERE course_id = c.id) as chapters,
                    (SELECT COUNT(*) FROM lessons l JOIN chapters ch ON l.chapter_id = ch.id WHERE ch.course_id = c.id) as lessons
                FROM {$this->table} c
                WHERE c.created_by = ?
                ORDER BY c.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get teacher's monthly enrollments
     * @param int $teacherId
     * @return array
     */
    public function getTeacherMonthlyEnrollments($teacherId) {
        $sql = "SELECT 
                    DATE_FORMAT(e.enrolled_at, '%Y-%m') as month,
                    COUNT(*) as count
                FROM enrollments e
                JOIN {$this->table} c ON e.course_id = c.id
                WHERE c.created_by = ? AND e.enrolled_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(e.enrolled_at, '%Y-%m')
                ORDER BY month ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get course status distribution
     * @return array
     */
    public function getStatusDistribution() {
        $sql = "SELECT status, COUNT(*) as count FROM {$this->table} GROUP BY status";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get total earnings
     * @return float
     */
    public function getTotalEarnings() {
        $sql = "SELECT COALESCE(SUM(price), 0) as total FROM {$this->table} WHERE price > 0";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return (float)($result['total'] ?? 0);
    }
    
    /**
     * Get earnings by status
     * @return array
     */
    public function getEarningsByStatus() {
        $sql = "SELECT status, SUM(price) as earnings, COUNT(*) as count 
                FROM {$this->table} 
                WHERE price > 0 
                GROUP BY status";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get monthly course stats
     * @return array
     */
    public function getMonthlyCourseStats() {
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as count
                FROM {$this->table}
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get course with chapters and lessons
     * @param int $id
     * @return array|null
     */
    public function getWithChapters($id) {
        $course = $this->getWithCreator($id);
        if (!$course) return null;
        
        require_once __DIR__ . '/../Model/Chapter.php';
        require_once __DIR__ . '/../Model/Lesson.php';
        
        $chapterModel = new Chapter();
        $lessonModel = new Lesson();
        
        $chapters = $chapterModel->getByCourseId($id);
        
        foreach ($chapters as &$chapter) {
            $chapter['lessons'] = $lessonModel->getByChapterId($chapter['id']);
        }
        
        $course['chapters'] = $chapters;
        $course['lesson_count'] = $lessonModel->getLessonCount($id);
        $course['total_duration'] = $lessonModel->getTotalDuration($id);
        
        return $course;
    }
    
    /**
     * Get all published courses
     * @return array
     */
    public function getPublished() {
        $sql = "SELECT c.*, u.name as creator_name
                FROM {$this->table} c
                JOIN users u ON c.created_by = u.id
                WHERE c.status = 'published'
                ORDER BY c.created_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get published courses with stats
     * @return array
     */
    public function getPublishedWithStats() {
        $sql = "SELECT c.*, u.name as creator_name,
                (SELECT COUNT(*) FROM chapters ch WHERE ch.course_id = c.id) as chapter_count,
                (SELECT COUNT(*) FROM lessons l JOIN chapters ch ON l.chapter_id = ch.id WHERE ch.course_id = c.id) as lesson_count
                FROM {$this->table} c
                JOIN users u ON c.created_by = u.id
                WHERE c.status = 'published'
                ORDER BY c.created_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get courses by status
     * @param string $status
     * @return array
     */
    public function getByStatus($status) {
        $sql = "SELECT c.*, u.name as creator_name,
                (SELECT COUNT(*) FROM chapters ch WHERE ch.course_id = c.id) as chapters_count,
                (SELECT COUNT(*) FROM lessons l JOIN chapters ch ON l.chapter_id = ch.id WHERE ch.course_id = c.id) as lessons_count
                FROM {$this->table} c
                JOIN users u ON c.created_by = u.id
                WHERE c.status = ?
                ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }
    
    /**
     * Count courses by status
     * @param string $status
     * @return int
     */
    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$status]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    /**
     * Count all courses
     * @return int
     */
    public function count(): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    /**
     * Update course status
     * @param int $id
     * @param string $status
     * @param string $adminMessage
     * @return bool
     */
    public function updateStatus($id, $status, $adminMessage = '') {
        $sql = "UPDATE {$this->table} SET status = ?, admin_message = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $adminMessage, $id]);
    }
}