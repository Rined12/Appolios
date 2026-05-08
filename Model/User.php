<?php
/**
 * APPOLIOS User Model
 * Entity class with attributes, constructor, getters and setters only
 * Database operations moved to Controller
 */

class User {
    // ==========================================
    // ATTRIBUTS (Private Properties)
    // ==========================================
    private ?int $id;
    private ?string $name;
    private ?string $email;
    private ?string $password;
    private ?string $role;
    private ?string $created_at;
    private ?string $avatar;
    private ?string $face_descriptor;
    private ?string $reset_token;
    private ?string $reset_token_expiry;
    private ?int $is_blocked;

    // ==========================================
    // CONSTRUCTEUR
    // ==========================================
    public function __construct(
        ?int $id = null,
        ?string $name = null,
        ?string $email = null,
        ?string $password = null,
        ?string $role = null,
        ?string $created_at = null,
        ?string $avatar = null,
        ?string $face_descriptor = null,
        ?string $reset_token = null,
        ?string $reset_token_expiry = null,
        ?int $is_blocked = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->created_at = $created_at;
        $this->avatar = $avatar;
        $this->face_descriptor = $face_descriptor;
        $this->reset_token = $reset_token;
        $this->reset_token_expiry = $reset_token_expiry;
        $this->is_blocked = $is_blocked;
    }

    // ==========================================
    // GETTERS & SETTERS
    // ==========================================
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getName(): ?string { return $this->name; }
    public function setName(?string $name): self { $this->name = $name; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(?string $password): self { $this->password = $password; return $this; }

    public function getRole(): ?string { return $this->role; }
    public function setRole(?string $role): self { $this->role = $role; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }

    public function getAvatar(): ?string { return $this->avatar; }
    public function setAvatar(?string $avatar): self { $this->avatar = $avatar; return $this; }

    public function getFaceDescriptor(): ?string { return $this->face_descriptor; }
    public function setFaceDescriptor(?string $face_descriptor): self { $this->face_descriptor = $face_descriptor; return $this; }

    public function getResetToken(): ?string { return $this->reset_token; }
    public function setResetToken(?string $reset_token): self { $this->reset_token = $reset_token; return $this; }

    public function getResetTokenExpiry(): ?string { return $this->reset_token_expiry; }
    public function setResetTokenExpiry(?string $reset_token_expiry): self { $this->reset_token_expiry = $reset_token_expiry; return $this; }

    public function getIsBlocked(): ?int { return $this->is_blocked; }
    public function setIsBlocked(?int $is_blocked): self { $this->is_blocked = $is_blocked; return $this; }
}