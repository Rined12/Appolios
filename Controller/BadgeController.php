<?php
/**
 * APPOLIOS Badge Controller
 * Handles badge-related business logic
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class BadgeController extends BaseModel {
    protected string $table = 'user_badges';
    
    public function getByUserId($userId) {
        $sql = "SELECT b.*, ub.awarded_at FROM user_badges ub JOIN badges b ON ub.badge_id = b.id WHERE ub.user_id = ? ORDER BY ub.awarded_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function hasBadge($userId, $badgeName) {
        $sql = "SELECT ub.id FROM user_badges ub JOIN badges b ON ub.badge_id = b.id WHERE ub.user_id = ? AND b.name = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $badgeName]);
        return $stmt->fetch() ? true : false;
    }

    public function awardBadge($userId, $badgeName, $icon, $description) {
        if ($this->hasBadge($userId, $badgeName)) {
            return false;
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("INSERT INTO badges (name, slug, description, icon) VALUES (?, ?, ?, ?)");
            $slug = strtolower(str_replace(' ', '-', $badgeName));
            $stmt->execute([$badgeName, $slug, $description, $icon]);
            $badgeId = $this->db->lastInsertId();
            
            $stmt = $this->db->prepare("INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)");
            $stmt->execute([$userId, $badgeId]);
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Badge award failed: ' . $e->getMessage());
            return false;
        }
    }
}