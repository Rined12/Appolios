<?php
/**
 * APPOLIOS Review Model
 * Handles course reviews and ratings
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class Review extends BaseModel {
    protected string $table = 'reviews';

    private ?int $id;
    private ?int $user_id;
    private ?int $course_id;
    private ?int $rating;
    private ?string $comment;
    private ?string $created_at;

    public function __construct(
        ?int $id = null,
        ?int $user_id = null,
        ?int $course_id = null,
        ?int $rating = null,
        ?string $comment = null,
        ?string $created_at = null
    ) {
        parent::__construct();
        
        $this->id = $id;
        $this->user_id = $user_id;
        $this->course_id = $course_id;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->created_at = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(?int $user_id): self { $this->user_id = $user_id; return $this; }

    public function getCourseId(): ?int { return $this->course_id; }
    public function setCourseId(?int $course_id): self { $this->course_id = $course_id; return $this; }

    public function getRating(): ?int { return $this->rating; }
    public function setRating(?int $rating): self { $this->rating = $rating; return $this; }

    public function getComment(): ?string { return $this->comment; }
    public function setComment(?string $comment): self { $this->comment = $comment; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }
    
    // Delegate methods for backward compatibility
    public function getByCourseId($courseId) {
        require_once __DIR__ . '/../Controller/ReviewController.php';
        $ctrl = new ReviewController();
        return $ctrl->getByCourseId($courseId);
    }
    
    public function getAverageRating($courseId) {
        require_once __DIR__ . '/../Controller/ReviewController.php';
        $ctrl = new ReviewController();
        return $ctrl->getAverageRating($courseId);
    }
    
    public function hasUserReviewed($userId, $courseId) {
        require_once __DIR__ . '/../Controller/ReviewController.php';
        $ctrl = new ReviewController();
        return $ctrl->hasUserReviewed($userId, $courseId);
    }
}