<?php
/**
 * APPOLIOS Certificate Model
 * Handles course completion certificates
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class Certificate extends BaseModel {
    protected string $table = 'certificates';

    private ?int $id;
    private ?int $user_id;
    private ?int $course_id;
    private ?string $certificate_number;
    private ?string $issued_at;

    public function __construct(
        ?int $id = null,
        ?int $user_id = null,
        ?int $course_id = null,
        ?string $certificate_number = null,
        ?string $issued_at = null
    ) {
        parent::__construct();
        
        $this->id = $id;
        $this->user_id = $user_id;
        $this->course_id = $course_id;
        $this->certificate_number = $certificate_number;
        $this->issued_at = $issued_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(?int $user_id): self { $this->user_id = $user_id; return $this; }

    public function getCourseId(): ?int { return $this->course_id; }
    public function setCourseId(?int $course_id): self { $this->course_id = $course_id; return $this; }

    public function getCertificateNumber(): ?string { return $this->certificate_number; }
    public function setCertificateNumber(?string $certificate_number): self { $this->certificate_number = $certificate_number; return $this; }

    public function getIssuedAt(): ?string { return $this->issued_at; }
    public function setIssuedAt(?string $issued_at): self { $this->issued_at = $issued_at; return $this; }
}