<?php
/**
 * APPOLIOS - Contact Message Model
 * Handles contact us messages storage and retrieval
 */

require_once __DIR__ . '/BaseRepository.php';

class ContactMessageRepository extends BaseRepository {
    protected string $table = 'contact_messages';

    /**
     * Create a new contact message
     * @param array $data
     * @return bool|int
     */
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

    /**
     * Get all messages with pagination
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllMessages($limit = 50, $offset = 0) {
        $sql = "SELECT cm.*, u.name AS reader_name
                FROM {$this->table} cm
                LEFT JOIN users u ON cm.read_by = u.id
                ORDER BY cm.created_at DESC
                LIMIT ? OFFSET ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get unread messages count
     * @return int
     */
    public function getUnreadCount() {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE is_read = 0";
        try {
            $stmt = $this->db->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Get single message by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $sql = "SELECT cm.*, u.name AS reader_name
                FROM {$this->table} cm
                LEFT JOIN users u ON cm.read_by = u.id
                WHERE cm.id = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Mark message as read
     * @param int $id
     * @param int $adminId
     * @return bool
     */
    public function markAsRead($id, $adminId) {
        $sql = "UPDATE {$this->table} 
                SET is_read = 1, read_by = ?, read_at = NOW() 
                WHERE id = ?";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$adminId, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Mark message as unread
     * @param int $id
     * @return bool
     */
    public function markAsUnread($id) {
        $sql = "UPDATE {$this->table} 
                SET is_read = 0, read_by = NULL, read_at = NULL 
                WHERE id = ?";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete message
     * @param int $id
     * @return bool
     */
    public function delete($id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
