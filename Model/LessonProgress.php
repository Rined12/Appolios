<?php
class LessonProgress {
    private ?int $id;
    private ?int $user_id;
    private ?int $lesson_id;
    private ?int $is_completed;
    private ?string $completed_at;

    public function __construct(
        ?int $id = null,
        ?int $user_id = null,
        ?int $lesson_id = null,
        ?int $is_completed = null,
        ?string $completed_at = null
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->lesson_id = $lesson_id;
        $this->is_completed = $is_completed;
        $this->completed_at = $completed_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(?int $user_id): self { $this->user_id = $user_id; return $this; }

    public function getLessonId(): ?int { return $this->lesson_id; }
    public function setLessonId(?int $lesson_id): self { $this->lesson_id = $lesson_id; return $this; }

    public function getIsCompleted(): ?int { return $this->is_completed; }
    public function setIsCompleted(?int $is_completed): self { $this->is_completed = $is_completed; return $this; }

    public function getCompletedAt(): ?string { return $this->completed_at; }
    public function setCompletedAt(?string $completed_at): self { $this->completed_at = $completed_at; return $this; }
}
