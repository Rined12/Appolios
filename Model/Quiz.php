<?php

declare(strict_types=1);

require_once __DIR__ . '/Entities/BaseEntity.php';

class Quiz extends BaseEntity
{
    private ?int $id;
    private int $chapterId;
    private string $title;
    private string $difficulty;
    private ?string $tags;
    private ?int $timeLimitSec;
    private string $questionsJson;
    private int $createdBy;
    private string $status;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id = null,
        int $chapterId = 0,
        string $title = '',
        string $difficulty = 'beginner',
        ?string $tags = null,
        ?int $timeLimitSec = null,
        string $questionsJson = '[]',
        int $createdBy = 0,
        string $status = 'approved',
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->chapterId = $chapterId;
        $this->title = $title;
        $this->difficulty = $difficulty;
        $this->tags = $tags;
        $this->timeLimitSec = $timeLimitSec;
        $this->questionsJson = $questionsJson;
        $this->createdBy = $createdBy;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getChapterId(): int { return $this->chapterId; }
    public function setChapterId(int $chapterId): void { $this->chapterId = $chapterId; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): void { $this->title = $title; }

    public function getDifficulty(): string { return $this->difficulty; }
    public function setDifficulty(string $difficulty): void { $this->difficulty = $difficulty; }

    public function getTags(): ?string { return $this->tags; }
    public function setTags(?string $tags): void { $this->tags = $tags; }

    public function getTimeLimitSec(): ?int { return $this->timeLimitSec; }
    public function setTimeLimitSec(?int $timeLimitSec): void { $this->timeLimitSec = $timeLimitSec; }

    public function getQuestionsJson(): string { return $this->questionsJson; }
    public function setQuestionsJson(string $questionsJson): void { $this->questionsJson = $questionsJson; }

    public function getCreatedBy(): int { return $this->createdBy; }
    public function setCreatedBy(int $createdBy): void { $this->createdBy = $createdBy; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): void { $this->status = $status; }

    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function setCreatedAt(?string $createdAt): void { $this->createdAt = $createdAt; }

    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function setUpdatedAt(?string $updatedAt): void { $this->updatedAt = $updatedAt; }
}
