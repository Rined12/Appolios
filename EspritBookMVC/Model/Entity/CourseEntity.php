<?php

declare(strict_types=1);

/** Domain model for a course (attributes + accessors). Persistence: CourseRepository. */
final class CourseEntity
{
    private ?int $id;
    private string $title;
    private string $description;
    private string $videoUrl;
    private int $createdBy;
    private ?string $createdAt;

    public function __construct(
        ?int $id,
        string $title,
        string $description,
        string $videoUrl,
        int $createdBy,
        ?string $createdAt = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->videoUrl = $videoUrl;
        $this->createdBy = $createdBy;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getVideoUrl(): string
    {
        return $this->videoUrl;
    }

    public function setVideoUrl(string $videoUrl): void
    {
        $this->videoUrl = $videoUrl;
    }

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(int $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
