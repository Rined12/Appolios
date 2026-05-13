<?php

declare(strict_types=1);

final class UserEntity
{
    private ?int $id;
    private string $name;
    private string $email;
    private ?string $passwordHash;
    private string $role;
    private ?string $createdAt;

    public function __construct(
        ?int $id,
        string $name,
        string $email,
        ?string $passwordHash,
        string $role = 'student',
        ?string $createdAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->role = $role;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(?string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
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

/** Pure data object for a contact message. */
final class ContactMessageEntity
{
    private ?int $id;
    private string $name;
    private string $email;
    private string $subject;
    private string $message;
    private int $isRead;

    public function __construct(
        ?int $id,
        string $name,
        string $email,
        string $subject,
        string $message,
        int $isRead = 0
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->isRead = $isRead;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $v): void
    {
        $this->name = $v;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $v): void
    {
        $this->email = $v;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $v): void
    {
        $this->subject = $v;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $v): void
    {
        $this->message = $v;
    }

    public function getIsRead(): int
    {
        return $this->isRead;
    }

    public function setIsRead(int $v): void
    {
        $this->isRead = $v;
    }
}

/** Domain model for a teacher application row. */
final class TeacherApplicationEntity
{
    private ?int $id;
    private string $name;
    private string $email;
    private string $status;

    public function __construct(?int $id, string $name, string $email, string $status = 'pending')
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->status = $status;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
