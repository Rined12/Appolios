<?php

require_once __DIR__ . '/../Model/BaseModel.php';

class Chapter extends BaseModel
{
    protected string $table = 'chapters';

    public function getByCourseId($courseId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE course_id = ? ORDER BY sort_order ASC, id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $courseId]);
        return $stmt->fetchAll();
    }

    public function findWithCourse($id)
    {
        $sql = "SELECT ch.*, c.created_by AS course_owner_id, c.title AS course_title
                FROM {$this->table} ch
                JOIN courses c ON c.id = ch.course_id
                WHERE ch.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $id]);
        return $stmt->fetch();
    }

    public function getAllForTeacher($teacherId): array
    {
        $sql = "SELECT ch.*, c.title AS course_title, c.id AS course_id, u.name AS author_name
                FROM {$this->table} ch
                JOIN courses c ON c.id = ch.course_id
                JOIN users u ON u.id = c.created_by
                WHERE c.created_by = ?
                ORDER BY c.title ASC, ch.sort_order ASC, ch.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $teacherId]);
        return $stmt->fetchAll();
    }

    public function getAllWithCourseAuthors(): array
    {
        $sql = "SELECT ch.*, c.title AS course_title, c.id AS course_id, u.name AS author_name
                FROM {$this->table} ch
                JOIN courses c ON c.id = ch.course_id
                JOIN users u ON u.id = c.created_by
                ORDER BY c.title ASC, ch.sort_order ASC, ch.id ASC";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function getAllWithCourseTitles(): array
    {
        $sql = "SELECT ch.*, c.title AS course_title
                FROM {$this->table} ch
                JOIN courses c ON c.id = ch.course_id
                ORDER BY c.title ASC, ch.sort_order ASC, ch.id ASC";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (course_id, title, content, sort_order) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            (int) ($data['course_id'] ?? 0),
            $data['title'] ?? '',
            $data['content'] ?? '',
            (int) ($data['sort_order'] ?? 0),
        ]);
        return $ok ? (int) $this->db->lastInsertId() : false;
    }

    public function update($id, $data): bool
    {
        $sql = "UPDATE {$this->table} SET title = ?, content = ?, sort_order = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'] ?? '',
            $data['content'] ?? '',
            (int) ($data['sort_order'] ?? 0),
            (int) $id,
        ]);
    }
}

