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

    /**
     * Hydrate from a typical users-table row (controller / repository row arrays).
     *
     * @param array<string, mixed> $row
     */
    public static function fromPersistedRow(array $row): self
    {
        return new self(
            isset($row['id']) ? (int) $row['id'] : null,
            (string) ($row['name'] ?? ''),
            (string) ($row['email'] ?? ''),
            isset($row['password']) ? (string) $row['password'] : null,
            (string) ($row['role'] ?? 'student'),
            isset($row['created_at']) ? (string) $row['created_at'] : null
        );
    }
}
