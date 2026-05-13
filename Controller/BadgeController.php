<?php
/**
 * APPOLIOS Badge Controller
 * Handles badge-related business logic
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class BadgeController extends BaseModel {
    protected string $table = 'user_badges';
    
    public function getByUserId($userId) {
        $sql = "SELECT b.id, b.name as badge_name, b.description as badge_description, b.icon as badge_icon, ub.awarded_at as earned_at 
                FROM user_badges ub 
                JOIN badges b ON ub.badge_id = b.id 
                WHERE ub.user_id = ? 
                ORDER BY ub.awarded_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function hasBadge($userId, $badgeName) {
        $sql = "SELECT ub.id FROM user_badges ub 
                JOIN badges b ON ub.badge_id = b.id 
                WHERE ub.user_id = ? AND b.name = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $badgeName]);
        return $stmt->fetch() ? true : false;
    }

    public function awardBadge($userId, $badgeName, $icon, $description) {
        if ($this->hasBadge($userId, $badgeName)) {
            return false;
        }

        // Find or create badge in badges table
        $sql = "SELECT id FROM badges WHERE name = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$badgeName]);
        $badge = $stmt->fetch();

        if ($badge) {
            $badgeId = $badge['id'];
            if ($icon || $description) {
                $update = "UPDATE badges SET icon = COALESCE(?, icon), description = COALESCE(?, description) WHERE id = ?";
                $stmt = $this->db->prepare($update);
                $stmt->execute([$icon, $description, $badgeId]);
            }
        } else {
            $insert = "INSERT INTO badges (name, icon, description, created_at) VALUES (?, ?, ?, NOW())";
            $stmt = $this->db->prepare($insert);
            $stmt->execute([$badgeName, $icon, $description]);
            $badgeId = $this->db->lastInsertId();
        }

        $sql = "INSERT INTO user_badges (user_id, badge_id, awarded_at) VALUES (?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $badgeId]);
    }

    public function getCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM user_badges WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return (int) ($stmt->fetch()['count'] ?? 0);
    }
}