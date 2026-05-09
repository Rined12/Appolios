<?php
/**
 * APPOLIOS Chapter Controller
 * Handles chapter-related business logic
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class ChapterController extends BaseModel {
    protected string $table = 'chapters';
    
    public function getByCourseId($courseId) {
        $sql = "SELECT * FROM {$this->table} WHERE course_id = ? ORDER BY sort_order ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    public function createChapter($data) {
        $maxOrder = $this->getMaxOrder($data['course_id'] ?? 0);
        $data['sort_order'] = $maxOrder + 1;
        return $this->create($data);
    }

    private function getMaxOrder($courseId) {
        $sql = "SELECT MAX(sort_order) as max_order FROM {$this->table} WHERE course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        $result = $stmt->fetch();
        return $result ? (int)$result['max_order'] : 0;
    }

    public function updateOrder($id, $newOrder) {
        return $this->update($id, ['sort_order' => $newOrder]);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (course_id, title, content, sort_order, created_at)
                VALUES (?, ?, ?, ?, NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['course_id'],
                $data['title'],
                $data['description'] ?? null,
                $data['sort_order'] ?? 1
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
        $order = $chapter['sort_order'];

        $this->delete($id);

        $this->db->query("UPDATE {$this->table} SET sort_order = sort_order - 1
                         WHERE course_id = $courseId AND sort_order > $order");

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