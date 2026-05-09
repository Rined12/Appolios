<?php
/**
 * APPOLIOS Certificate Controller
 * Handles certificate-related business logic
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class CertificateController extends BaseModel {
    protected string $table = 'certificates';
    
    public function generateCertificate($userId, $courseId) {
        require_once __DIR__ . '/../Model/Course.php';
        $courseModel = new Course();
        $course = $courseModel->findById($courseId);
        
        if (!$course) return false;

        $certCode = 'APP-' . date('Ymd') . '-' . strtoupper(substr(md5($userId . $courseId . time()), 0, 8));
        
        try {
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, course_id, certificate_code) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $courseId, $certCode]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getUserCertificates($userId) {
        $sql = "SELECT cert.*, c.title as course_title, c.description, u.name as creator_name
                FROM {$this->table} cert
                JOIN courses c ON cert.course_id = c.id
                JOIN users u ON c.created_by = u.id
                WHERE cert.user_id = ?
                ORDER BY cert.issued_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getCertificateById($id) {
        $sql = "SELECT cert.*, c.title as course_title, c.description, u.name as creator_name, usr.name as student_name, usr.email as student_email
                FROM {$this->table} cert
                JOIN courses c ON cert.course_id = c.id
                JOIN users u ON c.created_by = u.id
                JOIN users usr ON cert.user_id = usr.id
                WHERE cert.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function hasCertificate($userId, $courseId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    public function verifyCertificate($certificateCode) {
        $sql = "SELECT cert.*, c.title as course_title, u.name as student_name
                FROM {$this->table} cert
                JOIN courses c ON cert.course_id = c.id
                JOIN users u ON cert.user_id = u.id
                WHERE cert.certificate_code = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$certificateCode]);
        return $stmt->fetch();
    }

    public function getCertificateByCourse($userId, $courseId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetch();
    }

    public function delete($id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function countUserCertificates($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
}