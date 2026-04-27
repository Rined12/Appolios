<?php
/**
 * ActivityLog Model
 * Entity class with properties, constructor, getters and setters only
 * Database operations moved to Controller
 */

class ActivityLog
{
    // ==========================================
    // PROPERTIES (Private - Encapsulation)
    // ==========================================
    private ?int $id = null;
    private ?int $user_id = null;
    private ?string $user_name = null;
    private ?string $user_email = null;
    private ?string $user_role = null;
    private ?string $activity_type = null;
    private ?string $activity_description = null;
    private ?string $ip_address = null;
    private ?string $user_agent = null;
    private ?string $created_at = null;

    // ==========================================
    // CONSTRUCTOR
    // ==========================================
    public function __construct(
        ?int $id = null,
        ?int $user_id = null,
        ?string $user_name = null,
        ?string $user_email = null,
        ?string $user_role = null,
        ?string $activity_type = null,
        ?string $activity_description = null,
        ?string $ip_address = null,
        ?string $user_agent = null,
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->user_name = $user_name;
        $this->user_email = $user_email;
        $this->user_role = $user_role;
        $this->activity_type = $activity_type;
        $this->activity_description = $activity_description;
        $this->ip_address = $ip_address;
        $this->user_agent = $user_agent;
        $this->created_at = $created_at;
    }

    // ==========================================
    // GETTERS & SETTERS (One-line format)
    // ==========================================
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(?int $user_id): self { $this->user_id = $user_id; return $this; }

    public function getUserName(): ?string { return $this->user_name; }
    public function setUserName(?string $user_name): self { $this->user_name = $user_name; return $this; }

    public function getUserEmail(): ?string { return $this->user_email; }
    public function setUserEmail(?string $user_email): self { $this->user_email = $user_email; return $this; }

    public function getUserRole(): ?string { return $this->user_role; }
    public function setUserRole(?string $user_role): self { $this->user_role = $user_role; return $this; }

    public function getActivityType(): ?string { return $this->activity_type; }
    public function setActivityType(?string $activity_type): self { $this->activity_type = $activity_type; return $this; }

    public function getActivityDescription(): ?string { return $this->activity_description; }
    public function setActivityDescription(?string $activity_description): self { $this->activity_description = $activity_description; return $this; }

    public function getIpAddress(): ?string { return $this->ip_address; }
    public function setIpAddress(?string $ip_address): self { $this->ip_address = $ip_address; return $this; }

    public function getUserAgent(): ?string { return $this->user_agent; }
    public function setUserAgent(?string $user_agent): self { $this->user_agent = $user_agent; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }
}
