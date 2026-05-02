<?php
/**
 * APPOLIOS User XP Controller
 * Handles user XP-related business logic
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class UserXPController extends BaseModel {
    protected string $table = 'user_xp';
    
    public function __construct() {
        parent::__construct();
        $this->createTables();
    }
    
    private function createTables() {
        $sql1 = "CREATE TABLE IF NOT EXISTS user_xp (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL UNIQUE,
            xp INT DEFAULT 0,
            total_xp INT DEFAULT 0,
            last_xp_at DATETIME,
            INDEX idx_user (user_id)
        )";
        $this->db->exec($sql1);
        
        $sql2 = "CREATE TABLE IF NOT EXISTS xp_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            amount INT NOT NULL,
            reason VARCHAR(255),
            created_at DATETIME,
            INDEX idx_user (user_id)
        )";
        $this->db->exec($sql2);
    }
    
    public function getByUserId($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
    
    public function addXP($userId, $amount, $reason = '') {
        $current = $this->getByUserId($userId);
        
        if ($current) {
            $sql = "UPDATE {$this->table} SET xp = xp + ?, total_xp = total_xp + ?, last_xp_at = NOW() WHERE user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$amount, $amount, $userId]);
        } else {
            $sql = "INSERT INTO {$this->table} (user_id, xp, total_xp, last_xp_at) VALUES (?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $amount, $amount]);
        }
        
        $this->logXP($userId, $amount, $reason);
        
        return $this->getLevel($this->getByUserId($userId)['total_xp'] ?? 0);
    }
    
    private function logXP($userId, $amount, $reason) {
        $sql = "INSERT INTO xp_history (user_id, amount, reason, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $amount, $reason]);
    }
    
    public function getLevel($totalXP) {
        $levels = [
            ['name' => 'Private', 'min' => 0, 'icon' => '⭐'],
            ['name' => 'Corporal', 'min' => 100, 'icon' => '⭐⭐'],
            ['name' => 'Sergeant', 'min' => 250, 'icon' => '⭐⭐⭐'],
            ['name' => 'Lieutenant', 'min' => 500, 'icon' => '🔱'],
            ['name' => 'Captain', 'min' => 800, 'icon' => '🎖️'],
            ['name' => 'Major', 'min' => 1200, 'icon' => '⚔️'],
            ['name' => 'Colonel', 'min' => 1800, 'icon' => '🛡️'],
            ['name' => 'General', 'min' => 2500, 'icon' => '👑'],
            ['name' => 'Commander', 'min' => 3500, 'icon' => '🏆']
        ];
        
        $currentLevel = $levels[0];
        foreach ($levels as $level) {
            if ($totalXP >= $level['min']) {
                $currentLevel = $level;
            }
        }
        
        $nextLevelIndex = array_search($currentLevel, $levels) + 1;
        if ($nextLevelIndex < count($levels)) {
            $nextLevel = $levels[$nextLevelIndex];
            $progress = ($totalXP - $currentLevel['min']) / ($nextLevel['min'] - $currentLevel['min']) * 100;
            $progress = min(100, max(0, $progress));
        } else {
            $progress = 100;
        }
        
        return [
            'level' => $currentLevel['name'],
            'icon' => $currentLevel['icon'],
            'progress' => round($progress),
            'next_level' => $nextLevelIndex < count($levels) ? $nextLevel['name'] : null,
            'xp_to_next' => $nextLevelIndex < count($levels) ? $nextLevel['min'] - $totalXP : 0
        ];
    }
    
    public function getHistory($userId, $limit = 10) {
        $sql = "SELECT * FROM xp_history WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
}