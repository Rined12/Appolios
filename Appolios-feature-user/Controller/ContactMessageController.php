<?php
/**
 * APPOLIOS Contact Message Controller
 * Handles contact message-related business logic
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class ContactMessageController extends BaseModel {
    protected string $table = 'contact_messages';
    
    public function createMessage($data) {
        $sql = "INSERT INTO {$this->table} (name, email, subject, message)
                VALUES (?, ?, ?, ?)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['subject'],
                $data['message']
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("ContactMessage::createMessage error: " . $e->getMessage());
            return false;
        }
    }

    public function getAllMessages($limit = 50, $offset = 0) {
        $sql = "SELECT cm.*, u.name AS reader_name
                FROM {$this->table} cm
                LEFT JOIN users u ON cm.read_by = u.id
                ORDER BY cm.created_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function getUnreadCount() {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE is_read = 0";
        $stmt = $this->db->query($sql);
        return (int) $stmt->fetchColumn();
    }

    public function getById($id) {
        $sql = "SELECT cm.*, u.name AS reader_name
                FROM {$this->table} cm
                LEFT JOIN users u ON cm.read_by = u.id
                WHERE cm.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function markAsRead($id, $adminId) {
        $sql = "UPDATE {$this->table} 
                SET is_read = 1, read_by = ?, read_at = NOW() 
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$adminId, $id]);
    }

    public function markAsUnread($id) {
        $sql = "UPDATE {$this->table} 
                SET is_read = 0, read_by = NULL, read_at = NULL 
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function delete($id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}