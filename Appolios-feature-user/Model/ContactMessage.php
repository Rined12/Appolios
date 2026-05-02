<?php
/**
 * APPOLIOS - Contact Message Model
 * Handles contact us messages storage and retrieval
 */

require_once __DIR__ . '/BaseModel.php';

class ContactMessage extends BaseModel {
    protected string $table = 'contact_messages';

    private ?int $id;
    private ?string $name;
    private ?string $email;
    private ?string $subject;
    private ?string $message;
    private ?int $is_read;
    private ?int $read_by;
    private ?string $read_at;
    private ?string $created_at;

    public function __construct(
        ?int $id = null,
        ?string $name = null,
        ?string $email = null,
        ?string $subject = null,
        ?string $message = null,
        ?int $is_read = null,
        ?int $read_by = null,
        ?string $read_at = null,
        ?string $created_at = null
    ) {
        parent::__construct();
        
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->is_read = $is_read;
        $this->read_by = $read_by;
        $this->read_at = $read_at;
        $this->created_at = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getName(): ?string { return $this->name; }
    public function setName(?string $name): self { $this->name = $name; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }

    public function getSubject(): ?string { return $this->subject; }
    public function setSubject(?string $subject): self { $this->subject = $subject; return $this; }

    public function getMessage(): ?string { return $this->message; }
    public function setMessage(?string $message): self { $this->message = $message; return $this; }

    public function getIsRead(): ?int { return $this->is_read; }
    public function setIsRead(?int $is_read): self { $this->is_read = $is_read; return $this; }

    public function getReadBy(): ?int { return $this->read_by; }
    public function setReadBy(?int $read_by): self { $this->read_by = $read_by; return $this; }

    public function getReadAt(): ?string { return $this->read_at; }
    public function setReadAt(?string $read_at): self { $this->read_at = $read_at; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }
    
    // Delegate methods for backward compatibility
    public function getUnreadCount() {
        require_once __DIR__ . '/../Controller/ContactMessageController.php';
        $ctrl = new ContactMessageController();
        return $ctrl->getUnreadCount();
    }
    
    public function getAll() {
        require_once __DIR__ . '/../Controller/ContactMessageController.php';
        $ctrl = new ContactMessageController();
        return $ctrl->getAll();
    }
}