<?php
/**
 * APPOLIOS Evenement Resource Model
 * Handles rules, materiel, and day plans for evenement module
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class EvenementRessource extends BaseModel {
    protected string $table = 'evenement_ressources';

    private ?int $id;
    private ?int $evenement_id;
    private ?string $type;
    private ?string $title;
    private ?string $details;
    private ?int $created_by;
    private ?string $created_at;
    private ?string $updated_at;

    public function __construct(
        ?int $id = null,
        ?int $evenement_id = null,
        ?string $type = null,
        ?string $title = null,
        ?string $details = null,
        ?int $created_by = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        parent::__construct();
        
        $this->id = $id;
        $this->evenement_id = $evenement_id;
        $this->type = $type;
        $this->title = $title;
        $this->details = $details;
        $this->created_by = $created_by;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getEvenementId(): ?int { return $this->evenement_id; }
    public function setEvenementId(?int $evenement_id): self { $this->evenement_id = $evenement_id; return $this; }

    public function getType(): ?string { return $this->type; }
    public function setType(?string $type): self { $this->type = $type; return $this; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $title): self { $this->title = $title; return $this; }

    public function getDetails(): ?string { return $this->details; }
    public function setDetails(?string $details): self { $this->details = $details; return $this; }

    public function getCreatedBy(): ?int { return $this->created_by; }
    public function setCreatedBy(?int $created_by): self { $this->created_by = $created_by; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }

    public function getUpdatedAt(): ?string { return $this->updated_at; }
    public function setUpdatedAt(?string $updated_at): self { $this->updated_at = $updated_at; return $this; }
}