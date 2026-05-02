<?php
/**
 * Gamification Service
 * Handles XP, badges, and achievements
 */

require_once __DIR__ . '/../config/database.php';

class GamificationService {
    private $db;
    
    public function __construct() {
        $this->db = getConnection();
    }
    
    public function getUserXP($userId) {
        $sql = "SELECT * FROM user_xp WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        if (!$result) {
            $this->initializeUserXP($userId);
            return $this->getUserXP($userId);
        }
        
        return $result;
    }
    
    private function initializeUserXP($userId) {
        $sql = "INSERT INTO user_xp (user_id, xp_points, level) VALUES (?, 0, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
    }
    
    public function addXP($userId, $amount, $reason) {
        $this->db->beginTransaction();
        
        try {
            $sql = "INSERT INTO xp_transactions (user_id, xp_amount, reason) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $amount, $reason]);
            
            $sql = "UPDATE user_xp SET xp_points = xp_points + ? WHERE user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$amount, $userId]);
            
            $this->checkLevelUp($userId);
            $this->checkBadges($userId);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    private function checkLevelUp($userId) {
        $xp = $this->getUserXP($userId);
        $newLevel = $this->calculateLevel($xp['xp_points']);
        
        if ($newLevel > $xp['level']) {
            $sql = "UPDATE user_xp SET level = ? WHERE user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$newLevel, $userId]);
            
            $this->createNotification($userId, 'Level Up!', "Congratulations! You've reached level $newLevel!", 'achievement');
        }
    }
    
    private function calculateLevel($xp) {
        return floor(sqrt($xp / 100)) + 1;
    }
    
    public function getLevelProgress($userId) {
        $xp = $this->getUserXP($userId);
        $currentLevel = $xp['level'];
        $currentXP = $xp['xp_points'];
        
        $xpForCurrent = ($currentLevel - 1) * ($currentLevel - 1) * 100;
        $xpForNext = $currentLevel * $currentLevel * 100;
        
        $progress = (($currentXP - $xpForCurrent) / ($xpForNext - $xpForCurrent)) * 100;
        
        return [
            'level' => $currentLevel,
            'current_xp' => $currentXP,
            'xp_for_next' => $xpForNext,
            'progress' => min(100, max(0, $progress))
        ];
    }
    
    public function getUserBadges($userId) {
        $sql = "SELECT b.*, ub.earned_at 
                FROM user_badges ub 
                JOIN badges b ON ub.badge_id = b.id 
                WHERE ub.user_id = ? 
                ORDER BY ub.earned_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getAllBadges() {
        $sql = "SELECT * FROM badges ORDER BY criteria_value";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    private function checkBadges($userId) {
        $xp = $this->getUserXP($userId);
        
        $badges = $this->getAllBadges();
        $userBadges = array_column($this->getUserBadges($userId), 'id');
        
        foreach ($badges as $badge) {
            if (in_array($badge['id'], $userBadges)) continue;
            
            $earned = false;
            
            switch ($badge['criteria_type']) {
                case 'lessons_completed':
                    if ($xp['total_lessons_completed'] >= $badge['criteria_value']) $earned = true;
                    break;
                case 'courses_completed':
                    if ($xp['total_courses_completed'] >= $badge['criteria_value']) $earned = true;
                    break;
                case 'xp_earned':
                    if ($xp['xp_points'] >= $badge['criteria_value']) $earned = true;
                    break;
                case 'streak_days':
                    if ($xp['streak_days'] >= $badge['criteria_value']) $earned = true;
                    break;
            }
            
            if ($earned) {
                $this->awardBadge($userId, $badge['id']);
            }
        }
    }
    
    public function awardBadge($userId, $badgeId) {
        $sql = "INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $badgeId]);
        
        $sql = "SELECT name FROM badges WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$badgeId]);
        $badge = $stmt->fetch();
        
        if ($badge) {
            $sql = "UPDATE user_xp SET xp_points = xp_points + (SELECT xp_reward FROM badges WHERE id = ?) WHERE user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$badgeId, $userId]);
            
            $this->createNotification($userId, 'New Badge Earned!', "You earned the '" . $badge['name'] . "' badge!", 'badge');
        }
    }
    
    public function updateLessonCompletion($userId) {
        $sql = "UPDATE user_xp SET total_lessons_completed = total_lessons_completed + 1 WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        
        $this->addXP($userId, 5, 'Completed a lesson');
    }
    
    public function updateCourseCompletion($userId) {
        $sql = "UPDATE user_xp SET total_courses_completed = total_courses_completed + 1 WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        
        $this->addXP($userId, 100, 'Completed a course');
    }
    
    public function updateStreak($userId) {
        $xp = $this->getUserXP($userId);
        $today = date('Y-m-d');
        
        if ($xp['last_activity_date'] == $today) {
            return;
        }
        
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        if ($xp['last_activity_date'] == $yesterday) {
            $sql = "UPDATE user_xp SET streak_days = streak_days + 1, last_activity_date = ? WHERE user_id = ?";
        } else {
            $sql = "UPDATE user_xp SET streak_days = 1, last_activity_date = ? WHERE user_id = ?";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$today, $userId]);
    }
    
    private function createNotification($userId, $title, $message, $type) {
        $sql = "INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $title, $message, $type]);
    }
    
    public function getXPTransactions($userId, $limit = 20) {
        $sql = "SELECT * FROM xp_transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
}