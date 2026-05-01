<?php
/**
 * APPOLIOS Notification Model
 * Handles user notifications
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class Notification extends BaseModel {
    protected string $table = 'notifications';

    public function getByUserId($userId, $limit = 20) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (user_id, title, message, type, link, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['user_id'],
                $data['title'],
                $data['message'],
                $data['type'] ?? 'info',
                $data['link'] ?? null
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function markAsRead($id) {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function markAllAsRead($userId) {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }

    public function delete($id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}