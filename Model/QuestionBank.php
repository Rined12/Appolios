<?php

declare(strict_types=1);

require_once __DIR__ . '/Entities/BaseEntity.php';

class QuestionBank extends BaseEntity
{
    private ?int $id;
    private ?string $title;
    private string $questionText;
    private string $optionsJson;
    private int $correctAnswer;
    private ?string $tags;
    private string $difficulty;
    private int $createdBy;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id = null,
        ?string $title = null,
        string $questionText = '',
        string $optionsJson = '[]',
        int $correctAnswer = 0,
        ?string $tags = null,
        string $difficulty = 'beginner',
        int $createdBy = 0,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->questionText = $questionText;
        $this->optionsJson = $optionsJson;
        $this->correctAnswer = $correctAnswer;
        $this->tags = $tags;
        $this->difficulty = $difficulty;
        $this->createdBy = $createdBy;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $title): void { $this->title = $title; }

    public function getQuestionText(): string { return $this->questionText; }
    public function setQuestionText(string $questionText): void { $this->questionText = $questionText; }

    public function getOptionsJson(): string { return $this->optionsJson; }
    public function setOptionsJson(string $optionsJson): void { $this->optionsJson = $optionsJson; }

    public function getCorrectAnswer(): int { return $this->correctAnswer; }
    public function setCorrectAnswer(int $correctAnswer): void { $this->correctAnswer = $correctAnswer; }

    public function getTags(): ?string { return $this->tags; }
    public function setTags(?string $tags): void { $this->tags = $tags; }

    public function getDifficulty(): string { return $this->difficulty; }
    public function setDifficulty(string $difficulty): void { $this->difficulty = $difficulty; }

    public function getCreatedBy(): int { return $this->createdBy; }
    public function setCreatedBy(int $createdBy): void { $this->createdBy = $createdBy; }

    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function setCreatedAt(?string $createdAt): void { $this->createdAt = $createdAt; }

    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function setUpdatedAt(?string $updatedAt): void { $this->updatedAt = $updatedAt; }
}

