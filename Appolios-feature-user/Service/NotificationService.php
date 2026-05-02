<?php
/**
 * Notification Service
 * Handles user notifications
 */

require_once __DIR__ . '/../config/database.php';

class NotificationService {
    private $db;
    
    public function __construct() {
        $this->db = getConnection();
    }
    
    public function createNotification($userId, $title, $message, $type = 'info', $link = null) {
        $sql = "INSERT INTO notifications (user_id, title, message, type, link) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $title, $message, $type, $link]);
        
        return $this->db->lastInsertId();
    }
    
    public function getUserNotifications($userId, $limit = 20, $unreadOnly = false) {
        $sql = "SELECT * FROM notifications WHERE user_id = ?";
        
        if ($unreadOnly) {
            $sql .= " AND is_read = 0";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
    
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch()['count'] ?? 0;
    }
    
    public function markAsRead($notificationId, $userId) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$notificationId, $userId]);
        
        return true;
    }
    
    public function markAllAsRead($userId) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        
        return true;
    }
    
    public function deleteNotification($notificationId, $userId) {
        $sql = "DELETE FROM notifications WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$notificationId, $userId]);
        
        return true;
    }
    
    public function deleteOldNotifications($userId, $days = 30) {
        $sql = "DELETE FROM notifications 
                WHERE user_id = ? 
                AND is_read = 1 
                AND created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $days]);
        
        return true;
    }
    
    public function notifyCourseCompletion($userId, $courseTitle, $certificateCode) {
        $this->createNotification(
            $userId,
            'Course Completed! 🎉',
            "Congratulations! You've completed '$courseTitle'. Your certificate is ready!",
            'success',
            '?url=student/certificates'
        );
    }
    
    public function notifyNewBadge($userId, $badgeName, $xpEarned) {
        $this->createNotification(
            $userId,
            'New Badge Earned! 🏆',
            "You earned the '$badgeName' badge! +$xpEarned XP",
            'badge',
            '?url=student/badges'
        );
    }
    
    public function notifyNewCourse($userId, $courseTitle) {
        $this->createNotification(
            $userId,
            'New Course Available! 📚',
            "A new course '$courseTitle' has been added. Check it out!",
            'info',
            '?url=courses'
        );
    }
    
    public function notifyEventReminder($userId, $eventTitle, $eventDate) {
        $this->createNotification(
            $userId,
            'Event Reminder 📅',
            "Don't forget: '$eventTitle' is happening on $eventDate",
            'info',
            '?url=student/evenements'
        );
    }
}