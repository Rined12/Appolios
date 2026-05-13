<?php

declare(strict_types=1);

class QuizModel
{
    private ?int $id;
    private ?int $chapter_id;
    private ?string $title;
    private ?string $difficulty;
    private ?string $tags;
    private ?int $time_limit_sec;
    private ?string $questions_json;
    private ?int $created_by;
    private ?string $status;
    private ?string $created_at;
    private ?string $updated_at;

    public function __construct(
        ?int $id = null,
        ?int $chapter_id = null,
        ?string $title = null,
        ?string $difficulty = null,
        ?string $tags = null,
        ?int $time_limit_sec = null,
        ?string $questions_json = null,
        ?int $created_by = null,
        ?string $status = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->id = $id;
        $this->chapter_id = $chapter_id;
        $this->title = $title;
        $this->difficulty = $difficulty;
        $this->tags = $tags;
        $this->time_limit_sec = $time_limit_sec;
        $this->questions_json = $questions_json;
        $this->created_by = $created_by;
        $this->status = $status;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getChapterId(): ?int { return $this->chapter_id; }
    public function setChapterId(?int $chapter_id): self { $this->chapter_id = $chapter_id; return $this; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $title): self { $this->title = $title; return $this; }

    public function getDifficulty(): ?string { return $this->difficulty; }
    public function setDifficulty(?string $difficulty): self { $this->difficulty = $difficulty; return $this; }

    public function getTags(): ?string { return $this->tags; }
    public function setTags(?string $tags): self { $this->tags = $tags; return $this; }

    public function getTimeLimitSec(): ?int { return $this->time_limit_sec; }
    public function setTimeLimitSec(?int $time_limit_sec): self { $this->time_limit_sec = $time_limit_sec; return $this; }

    public function getQuestionsJson(): ?string { return $this->questions_json; }
    public function setQuestionsJson(?string $questions_json): self { $this->questions_json = $questions_json; return $this; }

    public function getCreatedBy(): ?int { return $this->created_by; }
    public function setCreatedBy(?int $created_by): self { $this->created_by = $created_by; return $this; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(?string $status): self { $this->status = $status; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }

    public function getUpdatedAt(): ?string { return $this->updated_at; }
    public function setUpdatedAt(?string $updated_at): self { $this->updated_at = $updated_at; return $this; }
}
