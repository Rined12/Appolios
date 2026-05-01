<?php
/**
 * CATEGORY MODEL
 */

class Category extends BaseModel {
    protected string $table = 'categories';
    protected string $primaryKey = 'id';

    public function getAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getCoursesCount($categoryId) {
        $sql = "SELECT COUNT(*) as count FROM courses WHERE category_id = ? AND status = 'approved'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetch()['count'] ?? 0;
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, description, icon, types) VALUES (?, ?, ?, ?)";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['name'],
                $data['description'] ?? '',
                $data['icon'] ?? 'folder',
                $data['types'] ?? ''
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET name = ?, description = ?, icon = ?, types = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['description'] ?? '',
            $data['icon'] ?? 'folder',
            $data['types'] ?? '',
            $id
        ]);
    }

    public function getTypesArray($category) {
        if (empty($category['types'])) {
            return [];
        }
        return array_map('trim', explode(',', $category['types']));
    }
}
