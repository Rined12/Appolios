<?php
/**
 * APPOLIOS Evenement Model
 * Handles evenement-related database operations
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class Evenement extends BaseModel {
    protected string $table = 'evenements';

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
        parent::__construct();
        
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

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $title): self { $this->title = $title; return $this; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(?string $titre): self { $this->titre = $titre; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getDateDebut(): ?string { return $this->date_debut; }
    public function setDateDebut(?string $date_debut): self { $this->date_debut = $date_debut; return $this; }

    public function getDateFin(): ?string { return $this->date_fin; }
    public function setDateFin(?string $date_fin): self { $this->date_fin = $date_fin; return $this; }

    public function getHeureDebut(): ?string { return $this->heure_debut; }
    public function setHeureDebut(?string $heure_debut): self { $this->heure_debut = $heure_debut; return $this; }

    public function getHeureFin(): ?string { return $this->heure_fin; }
    public function setHeureFin(?string $heure_fin): self { $this->heure_fin = $heure_fin; return $this; }

    public function getLieu(): ?string { return $this->lieu; }
    public function setLieu(?string $lieu): self { $this->lieu = $lieu; return $this; }

    public function getCapaciteMax(): ?int { return $this->capacite_max; }
    public function setCapaciteMax(?int $capacite_max): self { $this->capacite_max = $capacite_max; return $this; }

    public function getType(): ?string { return $this->type; }
    public function setType(?string $type): self { $this->type = $type; return $this; }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(?string $statut): self { $this->statut = $statut; return $this; }

    public function getApprovalStatus(): ?string { return $this->approval_status; }
    public function setApprovalStatus(?string $approval_status): self { $this->approval_status = $approval_status; return $this; }

    public function getLocation(): ?string { return $this->location; }
    public function setLocation(?string $location): self { $this->location = $location; return $this; }

    public function getEventDate(): ?string { return $this->event_date; }
    public function setEventDate(?string $event_date): self { $this->event_date = $event_date; return $this; }

    public function getCreatedBy(): ?int { return $this->created_by; }
    public function setCreatedBy(?int $created_by): self { $this->created_by = $created_by; return $this; }

    public function getApprovedBy(): ?int { return $this->approved_by; }
    public function setApprovedBy(?int $approved_by): self { $this->approved_by = $approved_by; return $this; }

    public function getApprovedAt(): ?string { return $this->approved_at; }
    public function setApprovedAt(?string $approved_at): self { $this->approved_at = $approved_at; return $this; }

    public function getRejectionReason(): ?string { return $this->rejection_reason; }
    public function setRejectionReason(?string $rejection_reason): self { $this->rejection_reason = $rejection_reason; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }

    public function getUpdatedAt(): ?string { return $this->updated_at; }
    public function setUpdatedAt(?string $updated_at): self { $this->updated_at = $updated_at; return $this; }
}