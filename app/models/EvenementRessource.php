<?php
/**
 * APPOLIOS Evenement Resource Model
 * Handles rules, materiel, and day plans for evenement module
 */

require_once __DIR__ . '/../core/Model.php';

class EvenementRessource extends Model {
    protected $table = 'evenement_ressources';

    public function __construct() {
        parent::__construct();
        $this->ensureTableStructure();
    }

    /**
     * Create or migrate table automatically for existing databases.
     * @return void
     */
    private function ensureTableStructure() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    evenement_id INT NOT NULL,
                    type ENUM('rule', 'materiel', 'plan') NOT NULL,
                    title VARCHAR(255) NOT NULL,
                    details TEXT DEFAULT NULL,
                    created_by INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE,
                    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
                    INDEX idx_evenement_id (evenement_id),
                    INDEX idx_evenement_type (evenement_id, type),
                    INDEX idx_type (type),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->db->exec($sql);

        $hasEvenementId = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'evenement_id'")->fetch();
        if (!$hasEvenementId) {
            $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN evenement_id INT NULL AFTER id");
        }

        $fkNameStmt = $this->db->prepare("SELECT CONSTRAINT_NAME
                                          FROM information_schema.KEY_COLUMN_USAGE
                                          WHERE TABLE_SCHEMA = DATABASE()
                                            AND TABLE_NAME = ?
                                            AND COLUMN_NAME = 'evenement_id'
                                            AND REFERENCED_TABLE_NAME IS NOT NULL
                                          LIMIT 1");
        $fkNameStmt->execute([$this->table]);
        $fkNameData = $fkNameStmt->fetch();

        if (!empty($fkNameData['CONSTRAINT_NAME'])) {
            $this->db->exec("ALTER TABLE {$this->table} DROP FOREIGN KEY {$fkNameData['CONSTRAINT_NAME']}");
        }

        // Remove orphan resources so we can enforce strict FK + NOT NULL.
        $this->db->exec("DELETE r
                        FROM {$this->table} r
                        LEFT JOIN evenements e ON e.id = r.evenement_id
                        WHERE r.evenement_id IS NULL OR e.id IS NULL");

        $columnInfo = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'evenement_id'")->fetch();
        if (isset($columnInfo['Null']) && strtoupper($columnInfo['Null']) === 'YES') {
            $this->db->exec("ALTER TABLE {$this->table} MODIFY COLUMN evenement_id INT NOT NULL");
        }

        // Backward-compatible type migration: material -> materiel.
        $this->db->exec("ALTER TABLE {$this->table} MODIFY COLUMN type ENUM('rule', 'material', 'materiel', 'plan') NOT NULL");
        $this->db->exec("UPDATE {$this->table} SET type = 'materiel' WHERE type = 'material'");
        $this->db->exec("ALTER TABLE {$this->table} MODIFY COLUMN type ENUM('rule', 'materiel', 'plan') NOT NULL");

        $hasEvenementIndex = $this->db->query("SHOW INDEX FROM {$this->table} WHERE Key_name = 'idx_evenement_id'")->fetch();
        if (!$hasEvenementIndex) {
            $this->db->exec("ALTER TABLE {$this->table} ADD INDEX idx_evenement_id (evenement_id)");
        }

        $hasEvenementTypeIndex = $this->db->query("SHOW INDEX FROM {$this->table} WHERE Key_name = 'idx_evenement_type'")->fetch();
        if (!$hasEvenementTypeIndex) {
            $this->db->exec("ALTER TABLE {$this->table} ADD INDEX idx_evenement_type (evenement_id, type)");
        }

        $this->db->exec("ALTER TABLE {$this->table}
                        ADD CONSTRAINT fk_ressource_evenement
                        FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE");
    }

    /**
     * Create one resource item.
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (evenement_id, type, title, details, created_by, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
            $data['evenement_id'],
                $data['type'],
                $data['title'],
                $data['details'],
                $data['created_by']
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Verify that a resource exists in the expected evenement/type list scope.
     * @param int $id
     * @param int $evenementId
     * @param string $type
     * @return bool
     */
    public function existsInListScope($id, $evenementId, $type) {
        $sql = "SELECT id FROM {$this->table} WHERE id = ? AND evenement_id = ? AND type = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $id, (int) $evenementId, $type]);
        return (bool) $stmt->fetch();
    }

    /**
     * Get resources by category type.
     * @param string $type
     * @return array
     */
    public function getByType($type) {
        $sql = "SELECT r.*, u.name as creator_name, e.title as evenement_title
                FROM {$this->table} r
                JOIN users u ON r.created_by = u.id
                JOIN evenements e ON r.evenement_id = e.id
                WHERE r.type = ?
                ORDER BY r.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }

    /**
     * Get resources by type for one evenement.
     * @param string $type
     * @param int $evenementId
     * @return array
     */
    public function getByTypeAndEvenement($type, $evenementId) {
        $sql = "SELECT r.*, u.name as creator_name, e.title as evenement_title
                FROM {$this->table} r
                JOIN users u ON r.created_by = u.id
                JOIN evenements e ON r.evenement_id = e.id
                WHERE r.type = ? AND r.evenement_id = ?
                ORDER BY r.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$type, $evenementId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all resources grouped by type for an evenement.
     * @param int $evenementId
     * @return array
     */
    public function getGroupedByEvenement($evenementId) {
        return [
            'rules' => $this->getByTypeAndEvenement('rule', $evenementId),
            'materiels' => $this->getByTypeAndEvenement('materiel', $evenementId),
            'plans' => $this->getByTypeAndEvenement('plan', $evenementId)
        ];
    }

    /**
     * Find one resource by ID.
     */
    public function findById(int|string $id): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    /**
     * Update one resource item.
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table}
                SET title = ?, details = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND evenement_id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['details'],
            $id,
            $data['evenement_id']
        ]);
    }

    /**
     * Delete one resource item.
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        return parent::delete($id);
    }

    /**
     * Delete one resource item by evenement scope.
     * @param int $id
     * @param int $evenementId
     * @return bool
     */
    public function deleteByEvenement($id, $evenementId) {
        $sql = "DELETE FROM {$this->table} WHERE id = ? AND evenement_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id, $evenementId]);
    }
}
