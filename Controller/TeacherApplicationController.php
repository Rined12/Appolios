<?php
/**
 * APPOLIOS Teacher Application Controller
 * Handles teacher application-related business logic
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class TeacherApplicationController extends BaseModel {
    protected string $table = 'teacher_applications';
    
    public function createApplication($data) {
        $sql = "INSERT INTO {$this->table} (user_id, motivation, cv_path, status) 
                VALUES (?, ?, ?, 'pending')";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['user_id'],
                $data['motivation'] ?? '',
                $data['cv_path'] ?? null
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getPendingApplications() {
        $sql = "SELECT ta.*, u.name, u.email 
                FROM {$this->table} ta
                JOIN users u ON ta.user_id = u.id
                WHERE ta.status = 'pending' 
                ORDER BY ta.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function approve($id, $adminId, $notes = '') {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE id = ?");
            $stmt->execute([$id]);
            $app = $stmt->fetch();
            
            if (!$app) {
                $this->db->rollBack();
                return false;
            }

            $sql = "UPDATE {$this->table} 
                    SET status = 'approved', reviewed_by = ?, reviewed_at = NOW(), admin_notes = ? 
                    WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$adminId, $notes, $id]);

            $sqlUser = "UPDATE users SET role = 'teacher' WHERE id = ?";
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute([$app['user_id']]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function reject($id, $adminId, $notes = '') {
        $sql = "UPDATE {$this->table} 
                SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW(), admin_notes = ? 
                WHERE id = ?";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$adminId, $notes, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function emailExists($email) {
        $sql = "SELECT ta.id FROM {$this->table} ta 
                JOIN users u ON ta.user_id = u.id 
                WHERE u.email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }

    public function countPending() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = 'pending'";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return (int) ($result['count'] ?? 0);
    }
}