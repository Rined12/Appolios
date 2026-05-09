<?php

declare(strict_types=1);

class StudentQuizFlagModel
{
    private ?int $id;
    private ?int $user_id;
    private ?int $quiz_id;
    private ?int $is_favorite;
    private ?int $is_redo;
    private ?string $created_at;
    private ?string $updated_at;

    public function __construct(
        ?int $id = null,
        ?int $user_id = null,
        ?int $quiz_id = null,
        ?int $is_favorite = null,
        ?int $is_redo = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->quiz_id = $quiz_id;
        $this->is_favorite = $is_favorite;
        $this->is_redo = $is_redo;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(?int $user_id): self { $this->user_id = $user_id; return $this; }

    public function getQuizId(): ?int { return $this->quiz_id; }
    public function setQuizId(?int $quiz_id): self { $this->quiz_id = $quiz_id; return $this; }

    public function getIsFavorite(): ?int { return $this->is_favorite; }
    public function setIsFavorite(?int $is_favorite): self { $this->is_favorite = $is_favorite; return $this; }

    public function getIsRedo(): ?int { return $this->is_redo; }
    public function setIsRedo(?int $is_redo): self { $this->is_redo = $is_redo; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }

    public function getUpdatedAt(): ?string { return $this->updated_at; }
    public function setUpdatedAt(?string $updated_at): self { $this->updated_at = $updated_at; return $this; }
}
