<?php
/**
 * Bookmark Service
 * Handles course bookmarks/favorites
 */

require_once __DIR__ . '/../config/database.php';

class BookmarkService {
    private $db;
    
    public function __construct() {
        $this->db = getConnection();
    }
    
    public function addBookmark($userId, $courseId) {
        $sql = "INSERT IGNORE INTO course_bookmarks (user_id, course_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        
        return true;
    }
    
    public function removeBookmark($userId, $courseId) {
        $sql = "DELETE FROM course_bookmarks WHERE user_id = ? AND course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        
        return true;
    }
    
    public function isBookmarked($userId, $courseId) {
        $sql = "SELECT id FROM course_bookmarks WHERE user_id = ? AND course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetch() !== false;
    }
    
    public function getUserBookmarks($userId) {
        $sql = "SELECT c.*, u.name as creator_name, cb.created_at as bookmarked_at
                FROM course_bookmarks cb
                JOIN courses c ON cb.course_id = c.id
                JOIN users u ON c.created_by = u.id
                WHERE cb.user_id = ?
                ORDER BY cb.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getBookmarkCount($courseId) {
        $sql = "SELECT COUNT(*) as count FROM course_bookmarks WHERE course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetch()['count'] ?? 0;
    }
    
    public function getMostBookmarkedCourses($limit = 10) {
        $sql = "SELECT c.*, u.name as creator_name, COUNT(cb.id) as bookmark_count
                FROM courses c
                JOIN users u ON c.created_by = u.id
                LEFT JOIN course_bookmarks cb ON c.id = cb.course_id
                WHERE c.status = 'approved'
                GROUP BY c.id
                ORDER BY bookmark_count DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function toggleBookmark($userId, $courseId) {
        if ($this->isBookmarked($userId, $courseId)) {
            $this->removeBookmark($userId, $courseId);
            return ['bookmarked' => false];
        } else {
            $this->addBookmark($userId, $courseId);
            return ['bookmarked' => true];
        }
    }
}