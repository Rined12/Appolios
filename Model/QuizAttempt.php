<?php

declare(strict_types=1);

require_once __DIR__ . '/Entities/BaseEntity.php';

class QuizAttempt extends BaseEntity
{
    private ?int $id;
    private int $userId;
    private int $quizId;
    private int $score;
    private int $total;
    private int $percentage;
    private ?string $submittedAt;

    public function __construct(
        ?int $id = null,
        int $userId = 0,
        int $quizId = 0,
        int $score = 0,
        int $total = 0,
        int $percentage = 0,
        ?string $submittedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->quizId = $quizId;
        $this->score = $score;
        $this->total = $total;
        $this->percentage = $percentage;
        $this->submittedAt = $submittedAt;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getUserId(): int { return $this->userId; }
    public function setUserId(int $userId): void { $this->userId = $userId; }

    public function getQuizId(): int { return $this->quizId; }
    public function setQuizId(int $quizId): void { $this->quizId = $quizId; }

    public function getScore(): int { return $this->score; }
    public function setScore(int $score): void { $this->score = $score; }

    public function getTotal(): int { return $this->total; }
    public function setTotal(int $total): void { $this->total = $total; }

    public function getPercentage(): int { return $this->percentage; }
    public function setPercentage(int $percentage): void { $this->percentage = $percentage; }

    public function getSubmittedAt(): ?string { return $this->submittedAt; }
    public function setSubmittedAt(?string $submittedAt): void { $this->submittedAt = $submittedAt; }
}

