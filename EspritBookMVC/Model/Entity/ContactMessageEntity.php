<?php

declare(strict_types=1);

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
