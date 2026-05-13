<?php
/**
 * Avatar Generator Service
 * Simple avatar generation using initials
 */

require_once __DIR__ . '/../config/database.php';

class AvatarGenerator {
    
    public function generateAvatar($faceData, $userId) {
        $db = getConnection();
        
        // Verify database connection
        $dbName = $db->query("SELECT DATABASE()")->fetchColumn();
        
        // Check users table columns first
        $cols = $db->query("DESCRIBE users")->fetchAll(PDO::FETCH_COLUMN);
        
        $stmt = $db->prepare("SELECT name FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $name = ($user && isset($user['name'])) ? $user['name'] : 'User';
        
        $initials = $this->getInitials($name);
        $colors = ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#ef4444'];
        $bgColor = $colors[array_rand($colors)];
        
        $avatarHtml = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
            <rect width="200" height="200" fill="' . $bgColor . '" rx="20"/>
            <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="white" font-size="72" font-weight="bold" font-family="Arial, sans-serif" dy=".1em">' . htmlspecialchars($initials) . '</text>
        </svg>';
        
        $avatarData = base64_encode($avatarHtml);
        
        try {
            $stmt = $db->prepare("INSERT INTO avatars (user_id, filename, avatar_data) VALUES (?, ?, ?)");
            $filename = 'avatar_' . $userId . '_' . time() . '.svg';
            $stmt->execute([$userId, $filename, $avatarData]);
            
            $avatarUrl = APP_ENTRY . '?url=student/get-avatar/' . $db->lastInsertId();
            
            return [
                'success' => true,
                'url' => $avatarUrl,
                'filename' => $filename
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Failed to save avatar'];
        }
    }
    
    private function getInitials($name) {
        $parts = explode(' ', trim($name));
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }
    
    public function getAvatar($userId) {
        $db = getConnection();
        
        $stmt = $db->prepare("SELECT avatar_data, filename FROM avatars WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        if ($result && $result['avatar_data']) {
            return [
                'success' => true,
                'data' => $result['avatar_data'],
                'type' => 'image/svg+xml'
            ];
        }
        
        return ['success' => false];
    }
    
    public function getAvatarById($avatarId) {
        $db = getConnection();
        
        $stmt = $db->prepare("SELECT avatar_data, filename FROM avatars WHERE id = ?");
        $stmt->execute([$avatarId]);
        $result = $stmt->fetch();
        
        if ($result && $result['avatar_data']) {
            return [
                'success' => true,
                'data' => $result['avatar_data'],
                'type' => 'image/svg+xml',
                'filename' => $result['filename']
            ];
        }
        
        return ['success' => false];
    }
}
