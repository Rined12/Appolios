<?php

/**
 * Evenement domain entity (attributes, constructor, getters/setters).
 */
class EvenementEntity
{
    private ?int $id;
    private ?string $title;
    private ?string $titre;
    private ?string $description;
    private ?string $date_debut;
    private ?string $date_fin;
    private ?string $heure_debut;
    private ?string $heure_fin;
    private ?string $lieu;
    private ?int $capacite_max;
    private ?string $type;
    private ?string $statut;
    private ?string $approval_status;
    private ?string $location;
    private ?string $event_date;
    private ?int $created_by;
    private ?int $approved_by;
    private ?string $approved_at;
    private ?string $rejection_reason;
    private ?string $created_at;
    private ?string $updated_at;

    public function __construct(
        ?int $id = null,
        ?string $title = null,
        ?string $titre = null,
        ?string $description = null,
        ?string $date_debut = null,
        ?string $date_fin = null,
        ?string $heure_debut = null,
        ?string $heure_fin = null,
        ?string $lieu = null,
        ?int $capacite_max = null,
        ?string $type = null,
        ?string $statut = null,
        ?string $approval_status = null,
        ?string $location = null,
        ?string $event_date = null,
        ?int $created_by = null,
        ?int $approved_by = null,
        ?string $approved_at = null,
        ?string $rejection_reason = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->titre = $titre;
        $this->description = $description;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->heure_debut = $heure_debut;
        $this->heure_fin = $heure_fin;
        $this->lieu = $lieu;
        $this->capacite_max = $capacite_max;
        $this->type = $type;
        $this->statut = $statut;
        $this->approval_status = $approval_status;
        $this->location = $location;
        $this->event_date = $event_date;
        $this->created_by = $created_by;
        $this->approved_by = $approved_by;
        $this->approved_at = $approved_at;
        $this->rejection_reason = $rejection_reason;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $v): void
    {
        $this->title = $v;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $v): void
    {
        $this->titre = $v;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $v): void
    {
        $this->description = $v;
    }

    public function getDateDebut(): ?string
    {
        return $this->date_debut;
    }

    public function setDateDebut(?string $v): void
    {
        $this->date_debut = $v;
    }

    public function getDateFin(): ?string
    {
        return $this->date_fin;
    }

    public function setDateFin(?string $v): void
    {
        $this->date_fin = $v;
    }

    public function getHeureDebut(): ?string
    {
        return $this->heure_debut;
    }

    public function setHeureDebut(?string $v): void
    {
        $this->heure_debut = $v;
    }

    public function getHeureFin(): ?string
    {
        return $this->heure_fin;
    }

    public function setHeureFin(?string $v): void
    {
        $this->heure_fin = $v;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $v): void
    {
        $this->lieu = $v;
    }

    public function getCapaciteMax(): ?int
    {
        return $this->capacite_max;
    }

    public function setCapaciteMax(?int $v): void
    {
        $this->capacite_max = $v;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $v): void
    {
        $this->type = $v;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $v): void
    {
        $this->statut = $v;
    }

    public function getApprovalStatus(): ?string
    {
        return $this->approval_status;
    }

    public function setApprovalStatus(?string $v): void
    {
        $this->approval_status = $v;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $v): void
    {
        $this->location = $v;
    }

    public function getEventDate(): ?string
    {
        return $this->event_date;
    }

    public function setEventDate(?string $v): void
    {
        $this->event_date = $v;
    }

    public function getCreatedBy(): ?int
    {
        return $this->created_by;
    }

    public function setCreatedBy(?int $v): void
    {
        $this->created_by = $v;
    }

    public function getApprovedBy(): ?int
    {
        return $this->approved_by;
    }

    public function setApprovedBy(?int $v): void
    {
        $this->approved_by = $v;
    }

    public function getApprovedAt(): ?string
    {
        return $this->approved_at;
    }

    public function setApprovedAt(?string $v): void
    {
        $this->approved_at = $v;
    }

    public function getRejectionReason(): ?string
    {
        return $this->rejection_reason;
    }

    public function setRejectionReason(?string $v): void
    {
        $this->rejection_reason = $v;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(?string $v): void
    {
        $this->created_at = $v;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?string $v): void
    {
        $this->updated_at = $v;
    }
}

/**
 * Evenement resource domain entity.
 */
class EvenementRessourceEntity
{
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
        $this->id = $id;
        $this->evenement_id = $evenement_id;
        $this->type = $type;
        $this->title = $title;
        $this->details = $details;
        $this->created_by = $created_by;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $v): void
    {
        $this->id = $v;
    }

    public function getEvenementId(): ?int
    {
        return $this->evenement_id;
    }

    public function setEvenementId(?int $v): void
    {
        $this->evenement_id = $v;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $v): void
    {
        $this->type = $v;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $v): void
    {
        $this->title = $v;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $v): void
    {
        $this->details = $v;
    }

    public function getCreatedBy(): ?int
    {
        return $this->created_by;
    }

    public function setCreatedBy(?int $v): void
    {
        $this->created_by = $v;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(?string $v): void
    {
        $this->created_at = $v;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?string $v): void
    {
        $this->updated_at = $v;
    }
}
