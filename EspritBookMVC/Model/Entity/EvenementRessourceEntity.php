<?php
/**
 * Evenement resource domain entity (attributes, constructor, getters/setters).
 * Persistence remains in RessourceController until an EvenementRessourceRepository is introduced.
 */

class EvenementRessourceEntity {

    private ?int    $id;
    private ?int    $evenement_id;
    private ?string $type;
    private ?string $title;
    private ?string $details;
    private ?int    $created_by;
    private ?string $created_at;
    private ?string $updated_at;

    public function __construct(
        ?int    $id           = null,
        ?int    $evenement_id = null,
        ?string $type         = null,
        ?string $title        = null,
        ?string $details      = null,
        ?int    $created_by   = null,
        ?string $created_at   = null,
        ?string $updated_at   = null
    ) {
        $this->id           = $id;
        $this->evenement_id = $evenement_id;
        $this->type         = $type;
        $this->title        = $title;
        $this->details      = $details;
        $this->created_by   = $created_by;
        $this->created_at   = $created_at;
        $this->updated_at   = $updated_at;
    }

    // ==========================================
    // GETTERS & SETTERS
    // ==========================================
    public function getId(): ?int                        { return $this->id; }
    public function setId(?int $v): self                 { $this->id = $v; return $this; }

    public function getEvenementId(): ?int               { return $this->evenement_id; }
    public function setEvenementId(?int $v): self        { $this->evenement_id = $v; return $this; }

    public function getType(): ?string                   { return $this->type; }
    public function setType(?string $v): self            { $this->type = $v; return $this; }

    public function getTitle(): ?string                  { return $this->title; }
    public function setTitle(?string $v): self           { $this->title = $v; return $this; }

    public function getDetails(): ?string                { return $this->details; }
    public function setDetails(?string $v): self         { $this->details = $v; return $this; }

    public function getCreatedBy(): ?int                 { return $this->created_by; }
    public function setCreatedBy(?int $v): self          { $this->created_by = $v; return $this; }

    public function getCreatedAt(): ?string              { return $this->created_at; }
    public function setCreatedAt(?string $v): self       { $this->created_at = $v; return $this; }

    public function getUpdatedAt(): ?string              { return $this->updated_at; }
    public function setUpdatedAt(?string $v): self       { $this->updated_at = $v; return $this; }
}
