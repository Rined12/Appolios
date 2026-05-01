<?php
/**
 * APPOLIOS Lesson Progress Model
 * Tracks lesson completion per user
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class LessonProgress extends BaseModel {
    protected string $table = 'lesson_progress';

    public function getByUserAndCourse($userId, $courseId) {
        $sql = "SELECT lp.*, l.title as lesson_title, ch.title as chapter_title
                FROM {$this->table} lp
                JOIN lessons l ON lp.lesson_id = l.id
                JOIN chapters ch ON l.chapter_id = ch.id
                WHERE lp.user_id = ? AND ch.course_id = ?
                ORDER BY ch.chapter_order, l.lesson_order";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetchAll();
    }

    public function getByLesson($userId, $lessonId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND lesson_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $lessonId]);
        return $stmt->fetch();
    }

    public function markComplete($userId, $lessonId) {
        $exists = $this->getByLesson($userId, $lessonId);
        
        if ($exists) {
            $sql = "UPDATE {$this->table} SET completed = 1, completed_at = NOW() WHERE user_id = ? AND lesson_id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$userId, $lessonId]);
        } else {
            $sql = "INSERT INTO {$this->table} (user_id, lesson_id, completed, completed_at) VALUES (?, ?, 1, NOW())";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$userId, $lessonId]);
        }
    }

    public function markCompleteNoDuplicate($userId, $lessonId) {
        $sql = "INSERT INTO {$this->table} (user_id, lesson_id, completed, completed_at) 
                VALUES (?, ?, 1, NOW())
                ON DUPLICATE KEY UPDATE completed = 1, completed_at = NOW()";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $lessonId]);
    }

    public function markIncomplete($userId, $lessonId) {
        $sql = "UPDATE {$this->table} SET completed = 0, completed_at = NULL WHERE user_id = ? AND lesson_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $lessonId]);
    }

    public function getCompletedCount($userId, $courseId) {
        $sql = "SELECT COUNT(DISTINCT lp.lesson_id) as count
                FROM {$this->table} lp
                JOIN lessons l ON lp.lesson_id = l.id
                JOIN chapters ch ON l.chapter_id = ch.id
                WHERE lp.user_id = ? AND ch.course_id = ? AND lp.completed = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function getTotalLessons($courseId) {
        $sql = "SELECT COUNT(*) as count
                FROM lessons l
                JOIN chapters ch ON l.chapter_id = ch.id
                WHERE ch.course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function calculateProgress($userId, $courseId) {
        $completed = $this->getCompletedCount($userId, $courseId);
        $total = $this->getTotalLessons($courseId);
        
        if ($total == 0) return 0;
        return round(($completed / $total) * 100);
    }

    public function updateWatchTime($userId, $lessonId, $seconds) {
        $exists = $this->getByLesson($userId, $lessonId);
        
        if ($exists) {
            $sql = "UPDATE {$this->table} SET watch_time = ? WHERE user_id = ? AND lesson_id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$seconds, $userId, $lessonId]);
        } else {
            $sql = "INSERT INTO {$this->table} (user_id, lesson_id, watch_time) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$userId, $lessonId, $seconds]);
        }
    }

    public function isLessonCompleted($userId, $lessonId) {
        $progress = $this->getByLesson($userId, $lessonId);
        return $progress && $progress['completed'] == 1;
    }

    public function getCourseCompletionPercentage($userId, $courseId) {
        return $this->calculateProgress($userId, $courseId);
    }

    public function getCompletedLessons($userId, $courseId) {
        $sql = "SELECT lp.lesson_id
                FROM {$this->table} lp
                JOIN lessons l ON lp.lesson_id = l.id
                JOIN chapters ch ON l.chapter_id = ch.id
                WHERE lp.user_id = ? AND ch.course_id = ? AND lp.completed = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetchAll();
    }
}