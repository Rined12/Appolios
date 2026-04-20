<?php
/**
 * APPOLIOS User Model
 * Handles user-related database operations
 */

require_once __DIR__ . '/../core/Model.php';

class User extends Model {
    protected $table = 'users';

    /**
     * Create a new user
     * @param array $data
     * @return int|false - User ID or false on failure
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => HASH_COST]),
                $data['role'] ?? 'student'
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Find user by email
     * @param string $email
     * @return array|null
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Authenticate user
     * @param string $email
     * @param string $password
     * @return array|false - User data or false
     */
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    /**
     * Update user
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $values[] = $value;
        }

        $values[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Get all students
     * @return array
     */
    public function getStudents() {
        $sql = "SELECT * FROM {$this->table} WHERE role = 'student'";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Count students
     * @return int
     */
    public function countStudents() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE role = 'student'";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['count'];
    }

    /**
     * Check if email exists
     * @param string $email
     * @return bool
     */
    public function emailExists($email) {
        return $this->findByEmail($email) !== false;
    }

    /**
     * Get all teachers
     * @return array
     */
    public function getTeachers() {
        $sql = "SELECT * FROM {$this->table} WHERE role = 'teacher' ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Find user by ID
     */
    public function findById(int|string $id): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }
}