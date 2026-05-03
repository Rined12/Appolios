<?php
/**
 * APPOLIOS Lesson Controller
 * Handles lesson-related business logic
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class LessonController extends BaseModel {
    protected string $table = 'lessons';
    
    public function getByChapterId($chapterId) {
        $sql = "SELECT * FROM {$this->table} WHERE chapter_id = ? ORDER BY lesson_order ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$chapterId]);
        return $stmt->fetchAll();
    }

    public function getByCourseId($courseId) {
        $sql = "SELECT l.*, c.title as chapter_title, c.chapter_order 
                FROM {$this->table} l 
                JOIN chapters c ON l.chapter_id = c.id 
                WHERE c.course_id = ? 
                ORDER BY c.chapter_order ASC, l.lesson_order ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    public function deleteByChapterId($chapterId) {
        $sql = "DELETE FROM {$this->table} WHERE chapter_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$chapterId]);
    }

    public function createLesson($data) {
        $maxOrder = $this->getMaxOrder($data['chapter_id'] ?? 0);
        $data['lesson_order'] = $maxOrder + 1;
        
        if (!isset($data['lesson_type'])) {
            $data['lesson_type'] = 'text';
        }

        return $this->create($data);
    }

    private function getMaxOrder($chapterId) {
        $sql = "SELECT MAX(lesson_order) as max_order FROM {$this->table} WHERE chapter_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$chapterId]);
        $result = $stmt->fetch();
        return $result ? (int)$result['max_order'] : 0;
    }

    public function updateOrder($id, $newOrder) {
        return $this->update($id, ['lesson_order' => $newOrder]);
    }

    public function getTotalDuration($courseId) {
        $sql = "SELECT SUM(l.duration) as total 
                FROM {$this->table} l 
                JOIN chapters c ON l.chapter_id = c.id 
                WHERE c.course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }

    public function getLessonCount($courseId) {
        $sql = "SELECT COUNT(l.id) as count 
                FROM {$this->table} l 
                JOIN chapters c ON l.chapter_id = c.id 
                WHERE c.course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        $result = $stmt->fetch();
        return $result ? (int)$result['count'] : 0;
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (chapter_id, title, lesson_type, content, pdf_path, duration, lesson_order, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['chapter_id'],
                $data['title'],
                $data['lesson_type'] ?? 'text',
                $data['content'] ?? null,
                $data['pdf_path'] ?? null,
                $data['duration'] ?? 0,
                $data['lesson_order'] ?? 1
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Lesson create error: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByCourseId($courseId) {
        $sql = "SELECT l.*, c.title as chapter_title, c.chapter_order
                FROM {$this->table} l
                JOIN chapters c ON l.chapter_id = c.id
                WHERE c.course_id = ?
                ORDER BY c.chapter_order ASC, l.lesson_order ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }
}