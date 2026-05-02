<?php
/**
 * APPOLIOS Lesson Progress Model
 * Tracks lesson completion per user
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class LessonProgress extends BaseModel {
    protected string $table = 'lesson_progress';

    private ?int $id;
    private ?int $user_id;
    private ?int $lesson_id;
    private ?int $completed;
    private ?int $watch_time;
    private ?string $completed_at;

    public function __construct(
        ?int $id = null,
        ?int $user_id = null,
        ?int $lesson_id = null,
        ?int $completed = null,
        ?int $watch_time = null,
        ?string $completed_at = null
    ) {
        parent::__construct();
        
        $this->id = $id;
        $this->user_id = $user_id;
        $this->lesson_id = $lesson_id;
        $this->completed = $completed;
        $this->watch_time = $watch_time;
        $this->completed_at = $completed_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(?int $user_id): self { $this->user_id = $user_id; return $this; }

    public function getLessonId(): ?int { return $this->lesson_id; }
    public function setLessonId(?int $lesson_id): self { $this->lesson_id = $lesson_id; return $this; }

    public function getCompleted(): ?int { return $this->completed; }
    public function setCompleted(?int $completed): self { $this->completed = $completed; return $this; }

    public function getWatchTime(): ?int { return $this->watch_time; }
    public function setWatchTime(?int $watch_time): self { $this->watch_time = $watch_time; return $this; }

    public function getCompletedAt(): ?string { return $this->completed_at; }
    public function setCompletedAt(?string $completed_at): self { $this->completed_at = $completed_at; return $this; }
    
    public function getCompletedLessons($userId, $courseId) {
        require_once __DIR__ . '/../Controller/LessonProgressController.php';
        $ctrl = new LessonProgressController();
        return $ctrl->getCompletedLessons($userId, $courseId);
    }
    
    public function getTotalLessons($courseId) {
        require_once __DIR__ . '/../Controller/LessonProgressController.php';
        $ctrl = new LessonProgressController();
        return $ctrl->getTotalLessons($courseId);
    }
    
    public function getCompletedCount($userId, $courseId) {
        require_once __DIR__ . '/../Controller/LessonProgressController.php';
        $ctrl = new LessonProgressController();
        return $ctrl->getCompletedCount($userId, $courseId);
    }
    
    public function markCompleteNoDuplicate($userId, $lessonId) {
        require_once __DIR__ . '/../Controller/LessonProgressController.php';
        $ctrl = new LessonProgressController();
        return $ctrl->markCompleteNoDuplicate($userId, $lessonId);
    }
}