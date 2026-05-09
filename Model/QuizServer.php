<?php

declare(strict_types=1);

class QuizServer
{
    private ?int $id;
    private int $courseId;
    private string $courseTitle;
    private int $chapterId;
    private string $chapterTitle;
    private string $title;
    private string $difficulty;
    private ?string $tags;
    private ?string $status;
    private ?string $createdAt;

    public function __construct(
        ?int $id = null,
        int $courseId = 0,
        string $courseTitle = '',
        int $chapterId = 0,
        string $chapterTitle = '',
        string $title = '',
        string $difficulty = 'beginner',
        ?string $tags = null,
        ?string $status = null,
        ?string $createdAt = null
    ) {
        $this->id = $id;
        $this->courseId = $courseId;
        $this->courseTitle = $courseTitle;
        $this->chapterId = $chapterId;
        $this->chapterTitle = $chapterTitle;
        $this->title = $title;
        $this->difficulty = $difficulty;
        $this->tags = $tags;
        $this->status = $status;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getCourseId(): int { return $this->courseId; }
    public function setCourseId(int $courseId): void { $this->courseId = $courseId; }

    public function getCourseTitle(): string { return $this->courseTitle; }
    public function setCourseTitle(string $courseTitle): void { $this->courseTitle = $courseTitle; }

    public function getChapterId(): int { return $this->chapterId; }
    public function setChapterId(int $chapterId): void { $this->chapterId = $chapterId; }

    public function getChapterTitle(): string { return $this->chapterTitle; }
    public function setChapterTitle(string $chapterTitle): void { $this->chapterTitle = $chapterTitle; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): void { $this->title = $title; }

    public function getDifficulty(): string { return $this->difficulty; }
    public function setDifficulty(string $difficulty): void { $this->difficulty = $difficulty; }

    public function getTags(): ?string { return $this->tags; }
    public function setTags(?string $tags): void { $this->tags = $tags; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(?string $status): void { $this->status = $status; }

    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function setCreatedAt(?string $createdAt): void { $this->createdAt = $createdAt; }
}
