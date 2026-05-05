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

    public function getCourseReview(int $userId, int $courseId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetch() ?: null;
    }

    public function getCourseReviews(int $courseId, int $limit = 10): array {
        $stmt = $this->db->prepare("SELECT r.*, u.name as user_name FROM {$this->table} r JOIN users u ON r.user_id = u.id WHERE r.course_id = ? ORDER BY r.created_at DESC LIMIT ?");
        $stmt->execute([$courseId, $limit]);
        return $stmt->fetchAll();
    }

    public function getCourseRating(int $courseId): array {
        $stmt = $this->db->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM {$this->table} WHERE course_id = ?");
        $stmt->execute([$courseId]);
        return $stmt->fetch() ?: ['avg_rating' => 0, 'total' => 0];
    }

    public function hasUserReviewed(int $userId, int $courseId): bool {
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$userId, $courseId]);
        return (bool) $stmt->fetch();
    }

    public function getAverageRating(int $courseId): array {
        $stmt = $this->db->prepare("SELECT AVG(rating) as avg, COUNT(*) as count FROM {$this->table} WHERE course_id = ?");
        $stmt->execute([$courseId]);
        return $stmt->fetch() ?: ['avg' => 0, 'count' => 0];
    }
}