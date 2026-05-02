<?php
/**
 * Category Service
 * Handles course categories
 */

require_once __DIR__ . '/../config/database.php';

class CategoryService {
    private $db;
    
    public function __construct() {
        $this->db = getConnection();
    }
    
    public function getAllCategories() {
        $sql = "SELECT c.*, 
                    (SELECT COUNT(*) FROM courses WHERE course_type = c.slug AND status = 'approved') as course_count
                FROM categories c
                ORDER BY c.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getCategoryBySlug($slug) {
        $sql = "SELECT * FROM categories WHERE slug = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
    
    public function getCategoryById($id) {
        $sql = "SELECT * FROM categories WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function createCategory($data) {
        $slug = $this->createSlug($data['name']);
        
        $sql = "INSERT INTO categories (name, slug, description, color, icon) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['name'],
            $slug,
            $data['description'] ?? null,
            $data['color'] ?? '#667eea',
            $data['icon'] ?? 'fa-book'
        ]);
        
        return $this->getCategoryById($this->db->lastInsertId());
    }
    
    public function updateCategory($id, $data) {
        $sql = "UPDATE categories SET name = ?, description = ?, color = ?, icon = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['color'] ?? '#667eea',
            $data['icon'] ?? 'fa-book',
            $id
        ]);
        
        return $this->getCategoryById($id);
    }
    
    public function deleteCategory($id) {
        $sql = "UPDATE courses SET course_type = NULL WHERE course_type = (SELECT slug FROM categories WHERE id = ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        $sql = "DELETE FROM categories WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        return true;
    }
    
    private function createSlug($name) {
        $slug = strtolower(trim(preg_replace('/[^a-z0-9-]/', '-', $name)));
        $slug = preg_replace('/-+/', '-', $slug);
        return rtrim($slug, '-');
    }
    
    public function getCoursesByCategory($slug, $limit = 20, $offset = 0) {
        $sql = "SELECT c.*, u.name as creator_name,
                    (SELECT AVG(rating) FROM course_reviews WHERE course_id = c.id AND is_approved = 1) as avg_rating
                FROM courses c
                JOIN users u ON c.created_by = u.id
                WHERE c.course_type = ? AND c.status = 'approved'
                ORDER BY c.created_at DESC
                LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug, $limit, $offset]);
        return $stmt->fetchAll();
    }
}