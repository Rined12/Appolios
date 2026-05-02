<?php
/**
 * APPOLIOS XP Model
 * Tracks user experience points and levels
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class UserXP extends BaseModel {
    protected string $table = 'user_xp';
    
    private ?int $id;
    private ?int $user_id;
    private ?int $xp;
    private ?int $total_xp;
    private ?string $last_xp_at;

    public function __construct(
        ?int $id = null,
        ?int $user_id = null,
        ?int $xp = null,
        ?int $total_xp = null,
        ?string $last_xp_at = null
    ) {
        parent::__construct();
        
        $this->id = $id;
        $this->user_id = $user_id;
        $this->xp = $xp;
        $this->total_xp = $total_xp;
        $this->last_xp_at = $last_xp_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(?int $user_id): self { $this->user_id = $user_id; return $this; }

    public function getXp(): ?int { return $this->xp; }
    public function setXp(?int $xp): self { $this->xp = $xp; return $this; }

    public function getTotalXp(): ?int { return $this->total_xp; }
    public function setTotalXp(?int $total_xp): self { $this->total_xp = $total_xp; return $this; }

    public function getLastXpAt(): ?string { return $this->last_xp_at; }
    public function setLastXpAt(?string $last_xp_at): self { $this->last_xp_at = $last_xp_at; return $this; }
    
    // Delegate methods for backward compatibility
    public function getByUserId($userId) {
        require_once __DIR__ . '/../Controller/UserXPController.php';
        $ctrl = new UserXPController();
        return $ctrl->getByUserId($userId);
    }
    
    public function addXP($userId, $amount, $reason = '') {
        require_once __DIR__ . '/../Controller/UserXPController.php';
        $ctrl = new UserXPController();
        return $ctrl->addXP($userId, $amount, $reason);
    }
    
    public function getLevel($totalXP) {
        require_once __DIR__ . '/../Controller/UserXPController.php';
        $ctrl = new UserXPController();
        return $ctrl->getLevel($totalXP);
    }
}