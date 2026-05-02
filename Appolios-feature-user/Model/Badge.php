<?php
/**
 * BADGE MODEL
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class Badge extends BaseModel {
    protected string $table = 'badges';
    protected string $primaryKey = 'id';

    private ?int $id;
    private ?int $user_id;
    private ?string $badge_name;
    private ?string $badge_icon;
    private ?string $badge_description;
    private ?string $earned_at;

    public function __construct(
        ?int $id = null,
        ?int $user_id = null,
        ?string $badge_name = null,
        ?string $badge_icon = null,
        ?string $badge_description = null,
        ?string $earned_at = null
    ) {
        parent::__construct();
        
        $this->id = $id;
        $this->user_id = $user_id;
        $this->badge_name = $badge_name;
        $this->badge_icon = $badge_icon;
        $this->badge_description = $badge_description;
        $this->earned_at = $earned_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(?int $user_id): self { $this->user_id = $user_id; return $this; }

    public function getBadgeName(): ?string { return $this->badge_name; }
    public function setBadgeName(?string $badge_name): self { $this->badge_name = $badge_name; return $this; }

    public function getBadgeIcon(): ?string { return $this->badge_icon; }
    public function setBadgeIcon(?string $badge_icon): self { $this->badge_icon = $badge_icon; return $this; }

    public function getBadgeDescription(): ?string { return $this->badge_description; }
    public function setBadgeDescription(?string $badge_description): self { $this->badge_description = $badge_description; return $this; }

    public function getEarnedAt(): ?string { return $this->earned_at; }
    public function setEarnedAt(?string $earned_at): self { $this->earned_at = $earned_at; return $this; }
    
    // Delegate methods for backward compatibility
    public function getByUserId($userId) {
        require_once __DIR__ . '/../Controller/BadgeController.php';
        $ctrl = new BadgeController();
        return $ctrl->getByUserId($userId);
    }
    
    public function hasBadge($userId, $badgeName) {
        require_once __DIR__ . '/../Controller/BadgeController.php';
        $ctrl = new BadgeController();
        return $ctrl->hasBadge($userId, $badgeName);
    }
    
    public function awardBadge($userId, $badgeName, $icon, $description) {
        require_once __DIR__ . '/../Controller/BadgeController.php';
        $ctrl = new BadgeController();
        return $ctrl->awardBadge($userId, $badgeName, $icon, $description);
    }
}