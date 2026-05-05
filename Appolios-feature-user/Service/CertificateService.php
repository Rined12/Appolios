<?php
/**
 * Certificate Service
 * Handles course completion certificates
 */

require_once __DIR__ . '/../config/database.php';

class CertificateService {
    private $db;
    
    public function __construct() {
        $this->db = getConnection();
        
        // Auto-create certificates table if it doesn't exist
        try {
            $this->db->query("SELECT 1 FROM certificates LIMIT 1");
        } catch (PDOException $e) {
            $this->db->exec("CREATE TABLE IF NOT EXISTS certificates (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                course_id INT NOT NULL,
                certificate_code VARCHAR(50) NOT NULL UNIQUE,
                issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                download_url VARCHAR(500),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
                UNIQUE KEY unique_user_course (user_id, course_id)
            )");
        }
    }
    
    public function generateCertificate($userId, $courseId) {
        $existing = $this->getCertificate($userId, $courseId);
        if ($existing) {
            return $existing;
        }
        
        $certificateCode = $this->generateCode($userId, $courseId);
        
        try {
            $sql = "INSERT INTO certificates (user_id, course_id, certificate_code) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $courseId, $certificateCode]);
            error_log("Certificate inserted: userId=$userId, courseId=$courseId, code=$certificateCode");
            
            return $this->getCertificate($userId, $courseId);
        } catch (PDOException $e) {
            error_log("Certificate INSERT error: " . $e->getMessage());
            return false;
        }
    }
    
    private function generateCode($userId, $courseId) {
        $prefix = 'APP';
        $timestamp = date('Ymd');
        $random = strtoupper(substr(md5($userId . $courseId . time()), 0, 8));
        return $prefix . '-' . $timestamp . '-' . $random;
    }
    
    public function getCertificate($userId, $courseId) {
        $sql = "SELECT c.*, u.name as student_name, u.email as student_email, 
                co.title as course_title, co.description as course_description,
                cr.name as creator_name
                FROM certificates c
                JOIN users u ON c.user_id = u.id
                JOIN courses co ON c.course_id = co.id
                JOIN users cr ON co.created_by = cr.id
                WHERE c.user_id = ? AND c.course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetch();
    }
    
    public function getUserCertificates($userId) {
        $sql = "SELECT c.*, co.title as course_title, co.description as course_description
                FROM certificates c
                JOIN courses co ON c.course_id = co.id
                WHERE c.user_id = ?
                ORDER BY c.issued_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function verifyCertificate($code) {
        $sql = "SELECT c.*, u.name as student_name, u.email as student_email,
                co.title as course_title
                FROM certificates c
                JOIN users u ON c.user_id = u.id
                JOIN courses co ON c.course_id = co.id
                WHERE c.certificate_code = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$code]);
        return $stmt->fetch();
    }
    
    public function getCertificateById($id) {
        $sql = "SELECT c.*, u.name as student_name, u.email as student_email,
                co.title as course_title, co.description as course_description,
                cr.name as creator_name
                FROM certificates c
                JOIN users u ON c.user_id = u.id
                JOIN courses co ON c.course_id = co.id
                JOIN users cr ON co.created_by = cr.id
                WHERE c.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function hasCompletedCourse($userId, $courseId) {
        $sql = "SELECT progress FROM enrollments WHERE user_id = ? AND course_id = ? AND progress >= 100";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetch() !== false;
    }
}