<?php

declare(strict_types=1);

class QuizAttemptModel
{
    private ?int $id;
    private ?int $user_id;
    private ?int $quiz_id;
    private ?int $score;
    private ?int $total;
    private ?int $percentage;
    private ?string $submitted_at;

    public function __construct(
        ?int $id = null,
        ?int $user_id = null,
        ?int $quiz_id = null,
        ?int $score = null,
        ?int $total = null,
        ?int $percentage = null,
        ?string $submitted_at = null
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->quiz_id = $quiz_id;
        $this->score = $score;
        $this->total = $total;
        $this->percentage = $percentage;
        $this->submitted_at = $submitted_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(?int $user_id): self { $this->user_id = $user_id; return $this; }

    public function getQuizId(): ?int { return $this->quiz_id; }
    public function setQuizId(?int $quiz_id): self { $this->quiz_id = $quiz_id; return $this; }

    public function getScore(): ?int { return $this->score; }
    public function setScore(?int $score): self { $this->score = $score; return $this; }

    public function getTotal(): ?int { return $this->total; }
    public function setTotal(?int $total): self { $this->total = $total; return $this; }

    public function getPercentage(): ?int { return $this->percentage; }
    public function setPercentage(?int $percentage): self { $this->percentage = $percentage; return $this; }

    public function getSubmittedAt(): ?string { return $this->submitted_at; }
    public function setSubmittedAt(?string $submitted_at): self { $this->submitted_at = $submitted_at; return $this; }
}
