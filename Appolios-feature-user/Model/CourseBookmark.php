<?php
/**
 * APPOLIOS Course Bookmark Model
 * Handles course bookmarks/favorites
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class CourseBookmark extends BaseModel {
    protected string = 'course_bookmarks';

    public function getUserBookmarks($userId) {
        $sql = "SELECT cb.*, c.title, c.description, c.image, c.price, u.name as creator_name
                FROM course_bookmarks cb
                JOIN courses c ON cb.course_id = c.id
                JOIN users u ON c.created_by = u.id
                WHERE cb.user_id = ?
                ORDER BY cb.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function addBookmark($userId, $courseId) {
        $sql = "INSERT INTO course_bookmarks (user_id, course_id, created_at) VALUES (?, ?, NOW())";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$userId, $courseId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function removeBookmark($userId, $courseId) {
        $sql = "DELETE FROM course_bookmarks WHERE user_id = ? AND course_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $courseId]);
    }

    public function isBookmarked($userId, $courseId) {
        $sql = "SELECT COUNT(*) as count FROM course_bookmarks WHERE user_id = ? AND course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    public function getBookmarksCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM course_bookmarks WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
}