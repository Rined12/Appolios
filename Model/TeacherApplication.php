<?php
/**
 * APPOLIOS Teacher Application Model
 * Entity class with attributes, constructor, getters and setters only
 * Database operations moved to Controller
 */

class TeacherApplication {
    // ==========================================
    // ATTRIBUTS (Private Properties)
    // ==========================================
    private ?int $id;
    private ?string $name;
    private ?string $email;
    private ?string $password;
    private ?string $cv_filename;
    private ?string $cv_path;
    private ?string $face_descriptor;
    private ?string $status;
    private ?string $created_at;
    private ?int $reviewed_by;
    private ?string $reviewed_at;
    private ?string $admin_notes;

    // ==========================================
    // CONSTRUCTEUR
    // ==========================================
    public function __construct(
        ?int $id = null,
        ?string $name = null,
        ?string $email = null,
        ?string $password = null,
        ?string $cv_filename = null,
        ?string $cv_path = null,
        ?string $face_descriptor = null,
        ?string $status = null,
        ?string $created_at = null,
        ?int $reviewed_by = null,
        ?string $reviewed_at = null,
        ?string $admin_notes = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->cv_filename = $cv_filename;
        $this->cv_path = $cv_path;
        $this->face_descriptor = $face_descriptor;
        $this->status = $status;
        $this->created_at = $created_at;
        $this->reviewed_by = $reviewed_by;
        $this->reviewed_at = $reviewed_at;
        $this->admin_notes = $admin_notes;
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

    public function getCvFilename(): ?string { return $this->cv_filename; }
    public function setCvFilename(?string $cv_filename): self { $this->cv_filename = $cv_filename; return $this; }

    public function getCvPath(): ?string { return $this->cv_path; }
    public function setCvPath(?string $cv_path): self { $this->cv_path = $cv_path; return $this; }

    public function getFaceDescriptor(): ?string { return $this->face_descriptor; }
    public function setFaceDescriptor(?string $face_descriptor): self { $this->face_descriptor = $face_descriptor; return $this; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(?string $status): self { $this->status = $status; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }

    public function getReviewedBy(): ?int { return $this->reviewed_by; }
    public function setReviewedBy(?int $reviewed_by): self { $this->reviewed_by = $reviewed_by; return $this; }

    public function getReviewedAt(): ?string { return $this->reviewed_at; }
    public function setReviewedAt(?string $reviewed_at): self { $this->reviewed_at = $reviewed_at; return $this; }

    public function getAdminNotes(): ?string { return $this->admin_notes; }
    public function setAdminNotes(?string $admin_notes): self { $this->admin_notes = $admin_notes; return $this; }
}
