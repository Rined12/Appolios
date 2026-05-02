<?php
/**
 * APPOLIOS Chapter Controller
 * Handles chapter-related business logic
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class ChapterController extends BaseModel {
    protected string $table = 'chapters';
    
    public function getByCourseId($courseId) {
        $sql = "SELECT * FROM {$this->table} WHERE course_id = ? ORDER BY chapter_order ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    public function createChapter($data) {
        $maxOrder = $this->getMaxOrder($data['course_id'] ?? 0);
        $data['chapter_order'] = $maxOrder + 1;
        return $this->create($data);
    }

    private function getMaxOrder($courseId) {
        $sql = "SELECT MAX(chapter_order) as max_order FROM {$this->table} WHERE course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        $result = $stmt->fetch();
        return $result ? (int)$result['max_order'] : 0;
    }

    public function updateOrder($id, $newOrder) {
        return $this->update($id, ['chapter_order' => $newOrder]);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (course_id, title, description, chapter_order, created_at)
                VALUES (?, ?, ?, ?, NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['course_id'],
                $data['title'],
                $data['description'] ?? null,
                $data['chapter_order'] ?? 1
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
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

    public function deleteChapter($id) {
        $chapter = $this->find($id);
        if (!$chapter) return false;

        $courseId = $chapter['course_id'];
        $order = $chapter['chapter_order'];

        $this->delete($id);

        $this->db->query("UPDATE {$this->table} SET chapter_order = chapter_order - 1
                         WHERE course_id = $courseId AND chapter_order > $order");

        return true;
    }

    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function find($id) {
        return $this->findById($id);
    }
    
    public function delete($id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}