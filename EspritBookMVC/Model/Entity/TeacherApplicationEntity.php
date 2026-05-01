<?php

declare(strict_types=1);

/** Domain model for a teacher application row. Persistence: TeacherApplicationRepository. */
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
