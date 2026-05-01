<?php

declare(strict_types=1);

/**
 * Pure data object for a discussion (attributes, constructor, getters/setters only).
 * Row mapping and rules live in repositories / controllers.
 */
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
