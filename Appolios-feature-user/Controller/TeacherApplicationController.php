<?php
/**
 * APPOLIOS Teacher Application Controller
 * Handles teacher application-related business logic
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class TeacherApplicationController extends BaseModel {
    protected string $table = 'teacher_applications';
    
    public function createApplication($data) {
        $sql = "INSERT INTO {$this->table} (name, email, password, cv_filename, cv_path, status) 
                VALUES (?, ?, ?, ?, ?, 'pending')";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['password'],
                $data['cv_filename'],
                $data['cv_path']
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getPendingApplications() {
        $sql = "SELECT * FROM v_pending_teachers ORDER BY created_at DESC";
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
        $sql = "UPDATE {$this->table} 
                SET status = 'approved', reviewed_by = ?, reviewed_at = NOW(), admin_notes = ? 
                WHERE id = ?";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$adminId, $notes, $id]);
        } catch (PDOException $e) {
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
        $sql = "SELECT id FROM {$this->table} WHERE email = ?";
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