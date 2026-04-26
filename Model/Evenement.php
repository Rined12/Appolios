<?php
/**
 * APPOLIOS Evenement Model
 * Data class only - properties, constructor, getters & setters
 * All database logic is in EventController
 */

class Evenement {

    private ?int    $id;
    private ?string $title;
    private ?string $titre;
    private ?string $description;
    private ?string $date_debut;
    private ?string $date_fin;
    private ?string $heure_debut;
    private ?string $heure_fin;
    private ?string $lieu;
    private ?int    $capacite_max;
    private ?string $type;
    private ?string $statut;
    private ?string $approval_status;
    private ?string $location;
    private ?string $event_date;
    private ?int    $created_by;
    private ?int    $approved_by;
    private ?string $approved_at;
    private ?string $rejection_reason;
    private ?string $created_at;
    private ?string $updated_at;

    public function __construct(
        ?int    $id               = null,
        ?string $title            = null,
        ?string $titre            = null,
        ?string $description      = null,
        ?string $date_debut       = null,
        ?string $date_fin         = null,
        ?string $heure_debut      = null,
        ?string $heure_fin        = null,
        ?string $lieu             = null,
        ?int    $capacite_max     = null,
        ?string $type             = null,
        ?string $statut           = null,
        ?string $approval_status  = null,
        ?string $location         = null,
        ?string $event_date       = null,
        ?int    $created_by       = null,
        ?int    $approved_by      = null,
        ?string $approved_at      = null,
        ?string $rejection_reason = null,
        ?string $created_at       = null,
        ?string $updated_at       = null
    ) {
        $this->id               = $id;
        $this->title            = $title;
        $this->titre            = $titre;
        $this->description      = $description;
        $this->date_debut       = $date_debut;
        $this->date_fin         = $date_fin;
        $this->heure_debut      = $heure_debut;
        $this->heure_fin        = $heure_fin;
        $this->lieu             = $lieu;
        $this->capacite_max     = $capacite_max;
        $this->type             = $type;
        $this->statut           = $statut;
        $this->approval_status  = $approval_status;
        $this->location         = $location;
        $this->event_date       = $event_date;
        $this->created_by       = $created_by;
        $this->approved_by      = $approved_by;
        $this->approved_at      = $approved_at;
        $this->rejection_reason = $rejection_reason;
        $this->created_at       = $created_at;
        $this->updated_at       = $updated_at;
    }

    // ==========================================
    // GETTERS & SETTERS
    // ==========================================
    public function getId(): ?int                       { return $this->id; }
    public function setId(?int $id): self               { $this->id = $id; return $this; }

    public function getTitle(): ?string                 { return $this->title; }
    public function setTitle(?string $v): self          { $this->title = $v; return $this; }

    public function getTitre(): ?string                 { return $this->titre; }
    public function setTitre(?string $v): self          { $this->titre = $v; return $this; }

    public function getDescription(): ?string           { return $this->description; }
    public function setDescription(?string $v): self    { $this->description = $v; return $this; }

    public function getDateDebut(): ?string             { return $this->date_debut; }
    public function setDateDebut(?string $v): self      { $this->date_debut = $v; return $this; }

    public function getDateFin(): ?string               { return $this->date_fin; }
    public function setDateFin(?string $v): self        { $this->date_fin = $v; return $this; }

    public function getHeureDebut(): ?string            { return $this->heure_debut; }
    public function setHeureDebut(?string $v): self     { $this->heure_debut = $v; return $this; }

    public function getHeureFin(): ?string              { return $this->heure_fin; }
    public function setHeureFin(?string $v): self       { $this->heure_fin = $v; return $this; }

    public function getLieu(): ?string                  { return $this->lieu; }
    public function setLieu(?string $v): self           { $this->lieu = $v; return $this; }

    public function getCapaciteMax(): ?int              { return $this->capacite_max; }
    public function setCapaciteMax(?int $v): self       { $this->capacite_max = $v; return $this; }

    public function getType(): ?string                  { return $this->type; }
    public function setType(?string $v): self           { $this->type = $v; return $this; }

    public function getStatut(): ?string                { return $this->statut; }
    public function setStatut(?string $v): self         { $this->statut = $v; return $this; }

    public function getApprovalStatus(): ?string        { return $this->approval_status; }
    public function setApprovalStatus(?string $v): self { $this->approval_status = $v; return $this; }

    public function getLocation(): ?string              { return $this->location; }
    public function setLocation(?string $v): self       { $this->location = $v; return $this; }

    public function getEventDate(): ?string             { return $this->event_date; }
    public function setEventDate(?string $v): self      { $this->event_date = $v; return $this; }

    public function getCreatedBy(): ?int                { return $this->created_by; }
    public function setCreatedBy(?int $v): self         { $this->created_by = $v; return $this; }

    public function getApprovedBy(): ?int               { return $this->approved_by; }
    public function setApprovedBy(?int $v): self        { $this->approved_by = $v; return $this; }

    public function getApprovedAt(): ?string            { return $this->approved_at; }
    public function setApprovedAt(?string $v): self     { $this->approved_at = $v; return $this; }

    public function getRejectionReason(): ?string       { return $this->rejection_reason; }
    public function setRejectionReason(?string $v): self { $this->rejection_reason = $v; return $this; }

    public function getCreatedAt(): ?string             { return $this->created_at; }
    public function setCreatedAt(?string $v): self      { $this->created_at = $v; return $this; }

    public function getUpdatedAt(): ?string             { return $this->updated_at; }
    public function setUpdatedAt(?string $v): self      { $this->updated_at = $v; return $this; }
}
