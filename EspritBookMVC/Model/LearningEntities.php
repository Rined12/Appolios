<?php

declare(strict_types=1);

/** Domain model for a course (attributes + accessors). */
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

/** Domain model for enrollment. */
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

/** Domain model for a group. */
final class GroupeEntity
{
    private ?int $idGroupe;
    private string $nomGroupe;
    private string $description;
    private int $idCreateur;
    private string $approvalStatut;
    private string $statut;

    public function __construct(
        ?int $idGroupe,
        string $nomGroupe,
        string $description,
        int $idCreateur,
        string $approvalStatut = 'en_cours',
        string $statut = 'actif'
    ) {
        $this->idGroupe = $idGroupe;
        $this->nomGroupe = $nomGroupe;
        $this->description = $description;
        $this->idCreateur = $idCreateur;
        $this->approvalStatut = $approvalStatut;
        $this->statut = $statut;
    }

    public function getIdGroupe(): ?int
    {
        return $this->idGroupe;
    }

    public function setIdGroupe(?int $id): void
    {
        $this->idGroupe = $id;
    }

    public function getNomGroupe(): string
    {
        return $this->nomGroupe;
    }

    public function setNomGroupe(string $v): void
    {
        $this->nomGroupe = $v;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $v): void
    {
        $this->description = $v;
    }

    public function getIdCreateur(): int
    {
        return $this->idCreateur;
    }

    public function setIdCreateur(int $v): void
    {
        $this->idCreateur = $v;
    }

    public function getApprovalStatut(): string
    {
        return $this->approvalStatut;
    }

    public function setApprovalStatut(string $v): void
    {
        $this->approvalStatut = $v;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $v): void
    {
        $this->statut = $v;
    }
}

/** Pure data object for a discussion. */
final class DiscussionEntity
{
    private ?int $id;
    private int $groupId;
    private int $authorUserId;
    private string $title;
    private string $content;
    private string $approvalStatus;

    public function __construct(
        ?int $id,
        int $groupId,
        int $authorUserId,
        string $title,
        string $content,
        string $approvalStatus = 'approuve'
    ) {
        $this->id = $id;
        $this->groupId = $groupId;
        $this->authorUserId = $authorUserId;
        $this->title = $title;
        $this->content = $content;
        $this->approvalStatus = $approvalStatus;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getAuthorUserId(): int
    {
        return $this->authorUserId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getApprovalStatus(): string
    {
        return $this->approvalStatus;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setGroupId(int $groupId): void
    {
        $this->groupId = $groupId;
    }

    public function setAuthorUserId(int $authorUserId): void
    {
        $this->authorUserId = $authorUserId;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setApprovalStatus(string $approvalStatus): void
    {
        $this->approvalStatus = $approvalStatus;
    }
}
