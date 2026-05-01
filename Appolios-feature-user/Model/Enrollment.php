<?php
/**
 * APPOLIOS Enrollment Model
 * Handles student course enrollments
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class Enrollment extends BaseModel {
    protected string $table = 'enrollments';

    // ==========================================
    // ENCAPSULATION: Private Properties
    // ==========================================
    private ?int $id;
    private ?int $user_id;
    private ?int $course_id;
    private ?int $progress;
    private ?string $enrolled_at;

    // ==========================================
    // CONSTRUCTOR
    // ==========================================
    public function __construct(
        ?int $id = null,
        ?int $user_id = null,
        ?int $course_id = null,
        ?int $progress = null,
        ?string $enrolled_at = null
    ) {
        parent::__construct();
        
        $this->id = $id;
        $this->user_id = $user_id;
        $this->course_id = $course_id;
        $this->progress = $progress;
        $this->enrolled_at = $enrolled_at;
    }

    // ==========================================
    // GETTERS & SETTERS
    // ==========================================
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(?int $user_id): self { $this->user_id = $user_id; return $this; }

    public function getCourseId(): ?int { return $this->course_id; }
    public function setCourseId(?int $course_id): self { $this->course_id = $course_id; return $this; }

    public function getProgress(): ?int { return $this->progress; }
    public function setProgress(?int $progress): self { $this->progress = $progress; return $this; }

    public function getEnrolledAt(): ?string { return $this->enrolled_at; }
    public function setEnrolledAt(?string $enrolled_at): self { $this->enrolled_at = $enrolled_at; return $this; }

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
     * Get single enrollment
     * @param int $userId
     * @param int $courseId
     * @return array|false
     */
    public function getEnrollment($userId, $courseId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetch();
    }

    /**
     * Get user's enrolled courses
     * @param int $userId
     * @return array
     */
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

    // ==========================================
    // STUDENT STATISTICS METHODS
    // ==========================================

    /**
     * Get student enrolled courses count
     * @param int $userId
     * @return int
     */
    public function getStudentEnrolledCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Get student completed courses count
     * @param int $userId
     * @return int
     */
    public function getStudentCompletedCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND progress = 100";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Get student in-progress courses count
     * @param int $userId
     * @return int
     */
    public function getStudentInProgressCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND progress > 0 AND progress < 100";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Get student average progress
     * @param int $userId
     * @return float
     */
    public function getStudentAverageProgress($userId) {
        $sql = "SELECT AVG(progress) as avg FROM {$this->table} WHERE user_id = ? AND progress > 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return (float)($result['avg'] ?? 0);
    }

    /**
     * Get student enrollment history (last 6 months)
     * @param int $userId
     * @return array
     */
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

    /**
     * Get student course progress details
     * @param int $userId
     * @return array
     */
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