<?php
/**
 * APPOLIOS Evenement Controller
 * Handles evenement-related business logic
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class EvenementController extends BaseModel {
    protected string $table = 'evenements';
    private string $lastError = '';

    public function __construct()
    {
        parent::__construct();
        $this->ensureTableStructure();
    }

    public function getLastError(): string
    {
        return $this->lastError;
    }

    private function ensureTableStructure(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    titre VARCHAR(255) DEFAULT NULL,
                    description TEXT NOT NULL,
                    date_debut DATE DEFAULT NULL,
                    date_fin DATE DEFAULT NULL,
                    heure_debut TIME DEFAULT NULL,
                    heure_fin TIME DEFAULT NULL,
                    lieu VARCHAR(255) DEFAULT NULL,
                    capacite_max INT DEFAULT NULL,
                    type VARCHAR(100) DEFAULT NULL,
                    statut ENUM('planifie', 'en_cours', 'termine', 'annule') DEFAULT 'planifie',
                    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
                    approved_by INT DEFAULT NULL,
                    approved_at DATETIME DEFAULT NULL,
                    rejection_reason TEXT DEFAULT NULL,
                    location VARCHAR(255) DEFAULT NULL,
                    event_date DATETIME NOT NULL,
                    created_by INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
                    INDEX idx_created_by (created_by),
                    INDEX idx_approval_status (approval_status)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->db->exec($sql);

        $this->ensureColumn('approval_status', "ENUM('pending', 'approved', 'rejected') DEFAULT 'approved' AFTER statut");
        $this->ensureColumn('approved_by', 'INT DEFAULT NULL AFTER approval_status');
        $this->ensureColumn('approved_at', 'DATETIME DEFAULT NULL AFTER approved_by');
        $this->ensureColumn('rejection_reason', 'TEXT DEFAULT NULL AFTER approved_at');
        $this->ensureColumn('location', 'VARCHAR(255) DEFAULT NULL AFTER statut');
        $this->ensureColumn('event_date', 'DATETIME NOT NULL AFTER location');
        $this->ensureColumn('created_by', 'INT NOT NULL AFTER event_date');

        $this->ensureIndex('idx_approval_status', 'approval_status');
    }

    private function ensureColumn(string $name, string $definition): void
    {
        $hasColumn = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE '" . $name . "'")->fetch();
        if (!$hasColumn) {
            $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN {$name} {$definition}");
        }
    }

    private function ensureIndex(string $indexName, string $columns): void
    {
        $hasIndex = $this->db->query("SHOW INDEX FROM {$this->table} WHERE Key_name = '" . $indexName . "'")->fetch();
        if (!$hasIndex) {
            $this->db->exec("ALTER TABLE {$this->table} ADD INDEX {$indexName} ({$columns})");
        }
    }
    
    public function create($data) {
        $this->lastError = '';
        $sql = "INSERT INTO {$this->table}
            (title, titre, description, date_debut, date_fin, heure_debut, heure_fin, lieu, capacite_max, type, statut, approval_status, location, event_date, created_by, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                $data['title'],
                $data['titre'],
                $data['description'],
                $data['date_debut'],
                $data['date_fin'],
                $data['heure_debut'],
                $data['heure_fin'],
                $data['lieu'],
                $data['capacite_max'],
                $data['type'],
                $data['statut'],
                $data['approval_status'] ?? 'approved',
                $data['location'],
                $data['event_date'],
                $data['created_by']
            ]);

            if (!$ok) {
                $info = $stmt->errorInfo();
                $this->lastError = isset($info[2]) ? (string) $info[2] : 'SQL error';
                return false;
            }

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table}
                SET title = ?,
                    titre = ?,
                    description = ?,
                    date_debut = ?,
                    date_fin = ?,
                    heure_debut = ?,
                    heure_fin = ?,
                    lieu = ?,
                    capacite_max = ?,
                    type = ?,
                    statut = ?,
                    location = ?,
                    event_date = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['titre'],
            $data['description'],
            $data['date_debut'],
            $data['date_fin'],
            $data['heure_debut'],
            $data['heure_fin'],
            $data['lieu'],
            $data['capacite_max'],
            $data['type'],
            $data['statut'],
            $data['location'],
            $data['event_date'],
            $id
        ]);
    }

    public function findAllUpcoming() {
        $hasRessourceTable = $this->db->query("SHOW TABLES LIKE 'evenement_ressources'")->fetch();

        if ($hasRessourceTable) {
            $sql = "SELECT e.*, u.name as creator_name, u.role as creator_role, COUNT(r.id) as resource_count
                    FROM {$this->table} e
                    JOIN users u ON e.created_by = u.id
                    LEFT JOIN evenement_ressources r ON r.evenement_id = e.id
                    GROUP BY e.id
                    ORDER BY COALESCE(CONCAT(e.date_debut, ' ', e.heure_debut), e.event_date) ASC";
        } else {
            $sql = "SELECT e.*, u.name as creator_name, u.role as creator_role, 0 as resource_count
                    FROM {$this->table} e
                    JOIN users u ON e.created_by = u.id
                    ORDER BY COALESCE(CONCAT(e.date_debut, ' ', e.heure_debut), e.event_date) ASC";
        }

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getRecent($limit = 3) {
        $sql = "SELECT *
                FROM {$this->table}
                ORDER BY COALESCE(CONCAT(date_debut, ' ', heure_debut), event_date) ASC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findApprovedUpcoming() {
        $hasRessourceTable = $this->db->query("SHOW TABLES LIKE 'evenement_ressources'")->fetch();

        if ($hasRessourceTable) {
            $sql = "SELECT e.*, u.name as creator_name, u.role as creator_role, COUNT(r.id) as resource_count
                    FROM {$this->table} e
                    JOIN users u ON e.created_by = u.id
                    LEFT JOIN evenement_ressources r ON r.evenement_id = e.id
                    WHERE e.approval_status = 'approved'
                    GROUP BY e.id
                    ORDER BY COALESCE(CONCAT(e.date_debut, ' ', e.heure_debut), e.event_date) ASC";
        } else {
            $sql = "SELECT e.*, u.name as creator_name, u.role as creator_role, 0 as resource_count
                    FROM {$this->table} e
                    JOIN users u ON e.created_by = u.id
                    WHERE e.approval_status = 'approved'
                    ORDER BY COALESCE(CONCAT(e.date_debut, ' ', e.heure_debut), e.event_date) ASC";
        }

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function findByIdWithCreator($id) {
        $sql = "SELECT e.*, u.name as creator_name, u.role as creator_role
                FROM {$this->table} e
                JOIN users u ON u.id = e.created_by
                WHERE e.id = ?
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $id]);
        return $stmt->fetch();
    }

    public function getByCreator($teacherId) {
        $sql = "SELECT e.*, COUNT(r.id) as resource_count
                FROM {$this->table} e
                LEFT JOIN evenement_ressources r ON r.evenement_id = e.id
                WHERE e.created_by = ?
                GROUP BY e.id
                ORDER BY e.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $teacherId]);
        return $stmt->fetchAll();
    }

    public function findByIdAndCreator($id, $creatorId) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND created_by = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $id, (int) $creatorId]);
        return $stmt->fetch();
    }

    public function getPendingTeacherRequests() {
        $sql = "SELECT e.*, u.name as creator_name, u.email as creator_email
                FROM {$this->table} e
                JOIN users u ON u.id = e.created_by
                WHERE e.approval_status = 'pending' AND u.role = 'teacher'
                ORDER BY e.created_at DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getRejectedTeacherRequests() {
        $sql = "SELECT e.*, u.name as creator_name, u.email as creator_email
                FROM {$this->table} e
                JOIN users u ON u.id = e.created_by
                WHERE e.approval_status = 'rejected' AND u.role = 'teacher'
                ORDER BY e.updated_at DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function updateApprovalStatus($id, $status, $adminId = null, $reason = null) {
        $normalized = strtolower((string) $status) === 'approved' ? 'approved' : 'rejected';

        $sql = "UPDATE {$this->table}
                SET approval_status = ?,
                    approved_by = ?,
                    approved_at = NOW(),
                    rejection_reason = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $normalized,
            $adminId !== null ? (int) $adminId : null,
            $normalized === 'rejected' ? $reason : null,
            (int) $id
        ]);
    }

    public function markPendingIfApproved($id) {
        $sql = "UPDATE {$this->table}
                SET approval_status = 'pending',
                    approved_by = NULL,
                    approved_at = NULL,
                    rejection_reason = NULL,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND approval_status = 'approved'";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([(int) $id]);
    }

    public function getAllWithCreator() {
        $sql = "SELECT e.*, u.name as creator_name, u.email as creator_email
                FROM {$this->table} e
                JOIN users u ON u.id = e.created_by
                ORDER BY e.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getParticipationMap(int $userId) {
        $sql = "SELECT evenement_id, details, created_at FROM evenement_ressources WHERE created_by = ? AND type = 'participation'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll();
        $map = [];
        foreach ($rows as $row) {
            $status = $row['details'] ?: 'pending';
            json_decode($status);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $status = json_encode(['status' => $status]);
            }
            $map[$row['evenement_id']] = [
                'details' => $status,
                'created_at' => $row['created_at']
            ];
        }
        return $map;
    }

    public function participate(int $eventId, int $userId) {
        $sql = "SELECT id FROM evenement_ressources WHERE evenement_id = ? AND created_by = ? AND type = 'participation'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$eventId, $userId]);
        if ($stmt->fetch()) return false;

        $sql = "INSERT INTO evenement_ressources (evenement_id, type, title, details, created_by, created_at) 
                VALUES (?, 'participation', 'Participation Request', 'pending', ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$eventId, $userId]);
    }

    public function cancelParticipation(int $eventId, int $userId) {
        $sql = "DELETE FROM evenement_ressources WHERE evenement_id = ? AND created_by = ? AND type = 'participation'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$eventId, $userId]);
    }

    public function getParticipationsByUser(int $userId) {
        $sql = "SELECT er.*, e.title as event_title, e.date_debut, e.heure_debut, e.lieu, e.type as event_type, e.statut as event_status
                FROM evenement_ressources er
                JOIN evenements e ON e.id = er.evenement_id
                WHERE er.created_by = ? AND er.type = 'participation'
                ORDER BY er.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll();
        
        foreach ($rows as &$row) {
            $status = $row['details'] ?: 'pending';
            json_decode($status);
            if (json_last_error() === JSON_ERROR_NONE) {
                $decoded = json_decode($status, true);
                $row['status'] = $decoded['status'] ?? 'pending';
            } else {
                $row['status'] = $status;
            }
        }
        return $rows;
    }

    public function getAllApprovedForStudent() {
        return $this->findApprovedUpcoming();
    }
}