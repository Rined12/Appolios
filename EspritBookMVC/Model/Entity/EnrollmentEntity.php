<?php

declare(strict_types=1);

/** Domain model for enrollment. Persistence: EnrollmentRepository. */
final class EnrollmentEntity
{
    private ?int $id;
    private int $userId;
    private int $courseId;
    private int $progress;
    private ?string $enrolledAt;

    public function __construct(?int $id, int $userId, int $courseId, int $progress, ?string $enrolledAt = null)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->courseId = $courseId;
        $this->progress = $progress;
        $this->enrolledAt = $enrolledAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getCourseId(): int
    {
        return $this->courseId;
    }

    public function setCourseId(int $courseId): void
    {
        $this->courseId = $courseId;
    }

    public function getProgress(): int
    {
        return $this->progress;
    }

    public function setProgress(int $progress): void
    {
        $this->progress = $progress;
    }

    public function getEnrolledAt(): ?string
    {
        return $this->enrolledAt;
    }

    public function setEnrolledAt(?string $enrolledAt): void
    {
        $this->enrolledAt = $enrolledAt;
    }
}
