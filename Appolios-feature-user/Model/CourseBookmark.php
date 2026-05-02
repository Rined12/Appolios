<?php
/**
 * APPOLIOS Course Bookmark Model
 * Handles course bookmarks/favorites
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class CourseBookmark extends BaseModel {
    protected string $table = 'course_bookmarks';

    private ?int $id;
    private ?int $user_id;
    private ?int $course_id;
    private ?string $created_at;

    public function __construct(
        ?int $id = null,
        ?int $user_id = null,
        ?int $course_id = null,
        ?string $created_at = null
    ) {
        parent::__construct();
        
        $this->id = $id;
        $this->user_id = $user_id;
        $this->course_id = $course_id;
        $this->created_at = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(?int $user_id): self { $this->user_id = $user_id; return $this; }

    public function getCourseId(): ?int { return $this->course_id; }
    public function setCourseId(?int $course_id): self { $this->course_id = $course_id; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }
    
    public function isBookmarked($userId, $courseId) {
        require_once __DIR__ . '/../Controller/CourseBookmarkController.php';
        $ctrl = new CourseBookmarkController();
        return $ctrl->isBookmarked($userId, $courseId);
    }
    
    public function addBookmark($userId, $courseId) {
        require_once __DIR__ . '/../Controller/CourseBookmarkController.php';
        $ctrl = new CourseBookmarkController();
        return $ctrl->addBookmark($userId, $courseId);
    }
    
    public function removeBookmark($userId, $courseId) {
        require_once __DIR__ . '/../Controller/CourseBookmarkController.php';
        $ctrl = new CourseBookmarkController();
        return $ctrl->removeBookmark($userId, $courseId);
    }
}