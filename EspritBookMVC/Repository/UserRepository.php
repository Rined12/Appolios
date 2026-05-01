<?php

require_once __DIR__ . '/BaseRepository.php';

/**
 * User persistence (PDO). Domain fields live in UserEntity.
 */
class UserRepository extends BaseRepository
{
    protected string $table = 'users';

    public function create(array $data): int|false
    {
        $sql = "INSERT INTO {$this->table} (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => HASH_COST]),
                $data['role'] ?? 'student',
            ]);

            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function authenticate(string $email, string $password): array|false
    {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $values[] = $value;
        }

        $values[] = $id;
        $sql = 'UPDATE ' . $this->table . ' SET ' . implode(', ', $fields) . ' WHERE id = ?';

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function getStudents(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE role = 'student'";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function countStudents(): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE role = 'student'";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return (int) ($result['count'] ?? 0);
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    public function getTeachers(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE role = 'teacher' ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function findById($id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function block(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET is_blocked = 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function unblock(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET is_blocked = 0 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function isBlocked(int $id): bool
    {
        $sql = "SELECT is_blocked FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result && (int) ($result['is_blocked'] ?? 0) === 1;
    }
}
