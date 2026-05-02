<?php
/**
 * APPOLIOS Badge Controller
 * Handles badge-related business logic
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class BadgeController extends BaseModel {
    protected string $table = 'badges';
    
    public function getByUserId($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY earned_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function hasBadge($userId, $badgeName) {
        $sql = "SELECT id FROM {$this->table} WHERE user_id = ? AND badge_name = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $badgeName]);
        return $stmt->fetch() ? true : false;
    }

    public function awardBadge($userId, $badgeName, $icon, $description) {
        if ($this->hasBadge($userId, $badgeName)) {
            return false;
        }

        $sql = "INSERT INTO {$this->table} (user_id, badge_name, badge_icon, badge_description) VALUES (?, ?, ?, ?)";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$userId, $badgeName, $icon, $description]);
        } catch (PDOException $e) {
            return false;
        }
    }
}