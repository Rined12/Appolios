<?php
/**
 * APPOLIOS Enrollment Model
 * Handles student course enrollments
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class Enrollment extends BaseModel {
    protected string $table = 'enrollments';

    private ?int $id;
    private ?int $user_id;
    private ?int $course_id;
    private ?int $progress;
    private ?string $enrolled_at;

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
    
    // Delegate methods for backward compatibility
    public function isEnrolled($userId, $courseId) {
        require_once __DIR__ . '/../Controller/EnrollmentController.php';
        $ctrl = new EnrollmentController();
        return $ctrl->isEnrolled($userId, $courseId);
    }
    
    public function getUserEnrollments($userId) {
        require_once __DIR__ . '/../Controller/EnrollmentController.php';
        $ctrl = new EnrollmentController();
        return $ctrl->getUserEnrollments($userId);
    }
    
    public function enroll($userId, $courseId) {
        require_once __DIR__ . '/../Controller/EnrollmentController.php';
        $ctrl = new EnrollmentController();
        return $ctrl->enroll($userId, $courseId);
    }
    
    public function getEnrollment($userId, $courseId) {
        require_once __DIR__ . '/../Controller/EnrollmentController.php';
        $ctrl = new EnrollmentController();
        return $ctrl->getEnrollment($userId, $courseId);
    }
    
    public function updateProgress($userId, $courseId, $progress) {
        require_once __DIR__ . '/../Controller/EnrollmentController.php';
        $ctrl = new EnrollmentController();
        return $ctrl->updateProgress($userId, $courseId, $progress);
    }
    
    public function getStudentEnrolledCount($userId) {
        require_once __DIR__ . '/../Controller/EnrollmentController.php';
        $ctrl = new EnrollmentController();
        return $ctrl->getStudentEnrolledCount($userId);
    }
    
    public function getStudentCompletedCount($userId) {
        require_once __DIR__ . '/../Controller/EnrollmentController.php';
        $ctrl = new EnrollmentController();
        return $ctrl->getStudentCompletedCount($userId);
    }
    
    public function getStudentInProgressCount($userId) {
        require_once __DIR__ . '/../Controller/EnrollmentController.php';
        $ctrl = new EnrollmentController();
        return $ctrl->getStudentInProgressCount($userId);
    }
    
    public function getStudentAverageProgress($userId) {
        require_once __DIR__ . '/../Controller/EnrollmentController.php';
        $ctrl = new EnrollmentController();
        return $ctrl->getStudentAverageProgress($userId);
    }
    
    public function getStudentEnrollmentHistory($userId) {
        require_once __DIR__ . '/../Controller/EnrollmentController.php';
        $ctrl = new EnrollmentController();
        return $ctrl->getStudentEnrollmentHistory($userId);
    }
    
    public function getStudentProgressDetails($userId) {
        require_once __DIR__ . '/../Controller/EnrollmentController.php';
        $ctrl = new EnrollmentController();
        return $ctrl->getStudentProgressDetails($userId);
    }
}