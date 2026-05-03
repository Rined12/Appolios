<?php
/**
 * Analytics Service
 * Dashboard statistics and reports
 */

require_once __DIR__ . '/../config/database.php';

class AnalyticsService {
    private $db;
    
    public function __construct() {
        $this->db = getConnection();
    }
    
    public function getOverviewStats() {
        $stats = [];
        
        $stats['total_users'] = $this->getTotalUsers();
        $stats['total_courses'] = $this->getTotalCourses();
        $stats['total_enrollments'] = $this->getTotalEnrollments();
        $stats['total_revenue'] = $this->getTotalRevenue();
        
        return $stats;
    }
    
    private function getTotalUsers() {
        $sql = "SELECT COUNT(*) as count FROM users WHERE is_blocked = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['count'];
    }
    
    private function getTotalCourses() {
        $sql = "SELECT COUNT(*) as count FROM courses WHERE status = 'approved'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['count'];
    }
    
    private function getTotalEnrollments() {
        $sql = "SELECT COUNT(*) as count FROM enrollments";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['count'];
    }
    
    private function getTotalRevenue() {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'succeeded'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (float) $stmt->fetch()['total'];
    }
    
    public function getEarningsByTeacher() {
        $sql = "SELECT 
                    u.id as teacher_id,
                    u.name as teacher_name,
                    COALESCE(SUM(p.amount), 0) as total_earnings,
                    COUNT(p.id) as payment_count
                FROM users u
                LEFT JOIN courses c ON c.created_by = u.id
                LEFT JOIN payments p ON p.course_id = c.id AND p.status = 'succeeded'
                WHERE u.role = 'teacher'
                GROUP BY u.id
                ORDER BY total_earnings DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getUserStats() {
        $sql = "SELECT 
                    role, 
                    COUNT(*) as count,
                    SUM(CASE WHEN is_blocked = 1 THEN 1 ELSE 0 END) as blocked
                FROM users 
                GROUP BY role";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetchAll();
        $stats = ['students' => 0, 'teachers' => 0, 'admins' => 0];
        
        foreach ($result as $row) {
            if ($row['role'] === 'student') {
                $stats['students'] = $row['count'];
            } elseif ($row['role'] === 'teacher') {
                $stats['teachers'] = $row['count'];
            } elseif ($row['role'] === 'admin') {
                $stats['admins'] = $row['count'];
            }
        }
        
        return $stats;
    }
    
    public function getCourseStats() {
        $sql = "SELECT 
                    status, 
                    COUNT(*) as count 
                FROM courses 
                GROUP BY status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetchAll();
        $stats = ['approved' => 0, 'pending' => 0, 'rejected' => 0];
        
        foreach ($result as $row) {
            $stats[$row['status']] = $row['count'];
        }
        
        return $stats;
    }
    
    public function getEnrollmentTrend($days = 30) {
        $sql = "SELECT 
                    DATE(enrolled_at) as date,
                    COUNT(*) as count
                FROM enrollments
                WHERE enrolled_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY DATE(enrolled_at)
                ORDER BY date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }
    
    public function getTopCourses($limit = 10) {
        $sql = "SELECT 
                    c.id, c.title, c.image,
                    u.name as creator,
                    COUNT(e.id) as enrollment_count,
                    COALESCE(AVG(r.rating), 0) as avg_rating
                FROM courses c
                JOIN users u ON c.created_by = u.id
                LEFT JOIN enrollments e ON c.id = e.course_id
                LEFT JOIN course_reviews r ON c.id = r.course_id AND r.is_approved = 1
                WHERE c.status = 'approved'
                GROUP BY c.id
                ORDER BY enrollment_count DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function getTopTeachers($limit = 10) {
        $sql = "SELECT 
                    u.id, u.name, u.email,
                    COUNT(c.id) as course_count,
                    COUNT(e.id) as total_enrollments
                FROM users u
                LEFT JOIN courses c ON u.id = c.created_by AND c.status = 'approved'
                LEFT JOIN enrollments e ON c.id = e.course_id
                WHERE u.role = 'teacher'
                GROUP BY u.id
                ORDER BY total_enrollments DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function getTopStudents($limit = 10) {
        $sql = "SELECT 
                    u.id, u.name, u.email,
                    COUNT(e.id) as enrolled_courses,
                    COALESCE(AVG(e.progress), 0) as avg_progress,
                    COALESCE(uxp.xp_points, 0) as xp_points,
                    COALESCE(uxp.level, 1) as level
                FROM users u
                LEFT JOIN enrollments e ON u.id = e.user_id
                LEFT JOIN user_xp uxp ON u.id = uxp.user_id
                WHERE u.role = 'student'
                GROUP BY u.id
                ORDER BY xp_points DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function getRecentActivity($limit = 20) {
        $activities = [];
        
        $sql = "SELECT 
                    'enrollment' as type,
                    e.enrolled_at as created_at,
                    u.name as user_name,
                    c.title as course_title,
                    'enrolled in' as action
                FROM enrollments e
                JOIN users u ON e.user_id = u.id
                JOIN courses c ON e.course_id = c.id
                ORDER BY e.enrolled_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll();
    }
    
    public function getCategoryDistribution() {
        $sql = "SELECT 
                    c.name,
                    c.color,
                    COUNT(co.id) as course_count,
                    COUNT(e.id) as enrollment_count
                FROM categories c
                LEFT JOIN courses co ON co.course_type = c.slug AND co.status = 'approved'
                LEFT JOIN enrollments e ON co.id = e.course_id
                GROUP BY c.id
                ORDER BY course_count DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getReviewStats() {
        $sql = "SELECT 
                    COUNT(*) as total_reviews,
                    COALESCE(AVG(rating), 0) as avg_rating,
                    SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END) as positive,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as neutral,
                    SUM(CASE WHEN rating <= 2 THEN 1 ELSE 0 END) as negative
                FROM course_reviews
                WHERE is_approved = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function getEventStats() {
        $sql = "SELECT 
                    statut,
                    COUNT(*) as count
                FROM evenements
                GROUP BY statut";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetchAll();
        $stats = ['upcoming' => 0, 'ongoing' => 0, 'completed' => 0, 'cancelled' => 0];
        
        foreach ($result as $row) {
            $stats[$row['statut']] = $row['count'];
        }
        
        return $stats;
    }
}