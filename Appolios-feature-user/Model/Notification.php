<?php
/**
 * APPOLIOS Notification Model
 * Handles user notifications
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class Notification extends BaseModel {
    protected string $table = 'notifications';

    private ?int $id;
    private ?int $user_id;
    private ?string $title;
    private ?string $message;
    private ?string $type;
    private ?string $link;
    private ?int $is_read;
    private ?string $created_at;

    public function __construct(
        ?int $id = null,
        ?int $user_id = null,
        ?string $title = null,
        ?string $message = null,
        ?string $type = null,
        ?string $link = null,
        ?int $is_read = null,
        ?string $created_at = null
    ) {
        parent::__construct();
        
        $this->id = $id;
        $this->user_id = $user_id;
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->link = $link;
        $this->is_read = $is_read;
        $this->created_at = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(?int $user_id): self { $this->user_id = $user_id; return $this; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $title): self { $this->title = $title; return $this; }

    public function getMessage(): ?string { return $this->message; }
    public function setMessage(?string $message): self { $this->message = $message; return $this; }

    public function getType(): ?string { return $this->type; }
    public function setType(?string $type): self { $this->type = $type; return $this; }

    public function getLink(): ?string { return $this->link; }
    public function setLink(?string $link): self { $this->link = $link; return $this; }

    public function getIsRead(): ?int { return $this->is_read; }
    public function setIsRead(?int $is_read): self { $this->is_read = $is_read; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }
}