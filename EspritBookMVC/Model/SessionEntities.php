<?php

declare(strict_types=1);

/**
 * Session-scoped DTOs (flash + validation messages). Accessors only.
 */
final class FlashMessageEntity
{
    private string $type;
    private string $message;

    public function __construct(string $type, string $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}

final class FormValidationMessagesEntity
{
    /** @var array<string, mixed> */
    private array $messages;

    /** @param array<string, mixed> $messages */
    public function __construct(array $messages = [])
    {
        $this->messages = $messages;
    }

    /** @return array<string, mixed> */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /** @param array<string, mixed> $messages */
    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }
}
