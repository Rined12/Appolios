<?php
/**
 * COURSEBADGE MODEL
 */

class CourseBadge extends BaseModel {
    protected string $table = 'course_badges';
    protected string $primaryKey = 'id';

    public function getByCourseId($courseId) {
        $sql = "SELECT * FROM {$this->table} WHERE course_id = ? ORDER BY created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (course_id, badge_name, badge_icon, badge_condition, description) VALUES (?, ?, ?, ?, ?)";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['course_id'],
                $data['badge_name'],
                $data['badge_icon'] ?? 'trophy',
                $data['badge_condition'] ?? 'completion',
                $data['description'] ?? ''
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete($id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}