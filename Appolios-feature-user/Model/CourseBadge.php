<?php
/**
 * COURSEBADGE MODEL
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class CourseBadge extends BaseModel {
    protected string $table = 'course_badges';
    protected string $primaryKey = 'id';

    private ?int $id;
    private ?int $course_id;
    private ?string $badge_name;
    private ?string $badge_icon;
    private ?string $badge_condition;
    private ?string $description;
    private ?string $created_at;

    public function __construct(
        ?int $id = null,
        ?int $course_id = null,
        ?string $badge_name = null,
        ?string $badge_icon = null,
        ?string $badge_condition = null,
        ?string $description = null,
        ?string $created_at = null
    ) {
        parent::__construct();
        
        $this->id = $id;
        $this->course_id = $course_id;
        $this->badge_name = $badge_name;
        $this->badge_icon = $badge_icon;
        $this->badge_condition = $badge_condition;
        $this->description = $description;
        $this->created_at = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getCourseId(): ?int { return $this->course_id; }
    public function setCourseId(?int $course_id): self { $this->course_id = $course_id; return $this; }

    public function getBadgeName(): ?string { return $this->badge_name; }
    public function setBadgeName(?string $badge_name): self { $this->badge_name = $badge_name; return $this; }

    public function getBadgeIcon(): ?string { return $this->badge_icon; }
    public function setBadgeIcon(?string $badge_icon): self { $this->badge_icon = $badge_icon; return $this; }

    public function getBadgeCondition(): ?string { return $this->badge_condition; }
    public function setBadgeCondition(?string $badge_condition): self { $this->badge_condition = $badge_condition; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }
}