<?php
/**
 * APPOLIOS Evenement Model
 * Handles evenement-related database operations
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class Evenement extends BaseModel {
    protected string $table = 'evenements';

    // ==========================================
    // ENCAPSULATION: Private Properties
    // ==========================================
    private ?int $id;
    private ?string $title;
    private ?string $titre;
    private ?string $description;
    private ?string $date_debut;
    private ?string $date_fin;
    private ?string $heure_debut;
    private ?string $heure_fin;
    private ?string $lieu;
    private ?int $capacite_max;
    private ?string $type;
    private ?string $statut;
    private ?string $approval_status;
    private ?string $location;
    private ?string $event_date;
    private ?int $created_by;
    private ?int $approved_by;
    private ?string $approved_at;
    private ?string $rejection_reason;
    private ?string $created_at;
    private ?string $updated_at;

    // ==========================================
    // CONSTRUCTOR
    // ==========================================
    public function __construct(
        ?int $id = null,
        ?string $title = null,
        ?string $titre = null,
        ?string $description = null,
        ?string $date_debut = null,
        ?string $date_fin = null,
        ?string $heure_debut = null,
        ?string $heure_fin = null,
        ?string $lieu = null,
        ?int $capacite_max = null,
        ?string $type = null,
        ?string $statut = null,
        ?string $approval_status = null,
        ?string $location = null,
        ?string $event_date = null,
        ?int $created_by = null,
        ?int $approved_by = null,
        ?string $approved_at = null,
        ?string $rejection_reason = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        parent::__construct();
        $this->ensureTableStructure();

        $this->id = $id;
        $this->title = $title;
        $this->titre = $titre;
        $this->description = $description;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->heure_debut = $heure_debut;
        $this->heure_fin = $heure_fin;
        $this->lieu = $lieu;
        $this->capacite_max = $capacite_max;
        $this->type = $type;
        $this->statut = $statut;
        $this->approval_status = $approval_status;
        $this->location = $location;
        $this->event_date = $event_date;
        $this->created_by = $created_by;
        $this->approved_by = $approved_by;
        $this->approved_at = $approved_at;
        $this->rejection_reason = $rejection_reason;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    // ==========================================
    // GETTERS & SETTERS
    // ==========================================
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $title): self { $this->title = $title; return $this; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(?string $titre): self { $this->titre = $titre; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getDateDebut(): ?string { return $this->date_debut; }
    public function setDateDebut(?string $date_debut): self { $this->date_debut = $date_debut; return $this; }

    public function getDateFin(): ?string { return $this->date_fin; }
    public function setDateFin(?string $date_fin): self { $this->date_fin = $date_fin; return $this; }

    public function getHeureDebut(): ?string { return $this->heure_debut; }
    public function setHeureDebut(?string $heure_debut): self { $this->heure_debut = $heure_debut; return $this; }

    public function getHeureFin(): ?string { return $this->heure_fin; }
    public function setHeureFin(?string $heure_fin): self { $this->heure_fin = $heure_fin; return $this; }

    public function getLieu(): ?string { return $this->lieu; }
    public function setLieu(?string $lieu): self { $this->lieu = $lieu; return $this; }

    public function getCapaciteMax(): ?int { return $this->capacite_max; }
    public function setCapaciteMax(?int $capacite_max): self { $this->capacite_max = $capacite_max; return $this; }

    public function getType(): ?string { return $this->type; }
    public function setType(?string $type): self { $this->type = $type; return $this; }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(?string $statut): self { $this->statut = $statut; return $this; }

    public function getApprovalStatus(): ?string { return $this->approval_status; }
    public function setApprovalStatus(?string $approval_status): self { $this->approval_status = $approval_status; return $this; }

    public function getLocation(): ?string { return $this->location; }
    public function setLocation(?string $location): self { $this->location = $location; return $this; }

    public function getEventDate(): ?string { return $this->event_date; }
    public function setEventDate(?string $event_date): self { $this->event_date = $event_date; return $this; }

    public function getCreatedBy(): ?int { return $this->created_by; }
    public function setCreatedBy(?int $created_by): self { $this->created_by = $created_by; return $this; }

    public function getApprovedBy(): ?int { return $this->approved_by; }
    public function setApprovedBy(?int $approved_by): self { $this->approved_by = $approved_by; return $this; }

    public function getApprovedAt(): ?string { return $this->approved_at; }
    public function setApprovedAt(?string $approved_at): self { $this->approved_at = $approved_at; return $this; }

    public function getRejectionReason(): ?string { return $this->rejection_reason; }
    public function setRejectionReason(?string $rejection_reason): self { $this->rejection_reason = $rejection_reason; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }

    public function getUpdatedAt(): ?string { return $this->updated_at; }
    public function setUpdatedAt(?string $updated_at): self { $this->updated_at = $updated_at; return $this; }

    /**
     * Create/migrate evenements table automatically for older databases.
     * @return void
     */
    private function ensureTableStructure() {
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
                    location VARCHAR(255) DEFAULT NULL,
                    event_date DATETIME NOT NULL,
                    created_by INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
                    INDEX idx_date_debut (date_debut),
                    INDEX idx_event_date (event_date),
                    INDEX idx_created_by (created_by)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->db->exec($sql);

        $this->ensureColumn('titre', 'VARCHAR(255) DEFAULT NULL AFTER title');
        $this->ensureColumn('date_debut', 'DATE DEFAULT NULL AFTER description');
        $this->ensureColumn('date_fin', 'DATE DEFAULT NULL AFTER date_debut');
        $this->ensureColumn('heure_debut', 'TIME DEFAULT NULL AFTER date_fin');
        $this->ensureColumn('heure_fin', 'TIME DEFAULT NULL AFTER heure_debut');
        $this->ensureColumn('lieu', 'VARCHAR(255) DEFAULT NULL AFTER heure_fin');
        $this->ensureColumn('capacite_max', 'INT DEFAULT NULL AFTER lieu');
        $this->ensureColumn('type', 'VARCHAR(100) DEFAULT NULL AFTER capacite_max');
        $this->ensureColumn("statut", "ENUM('planifie', 'en_cours', 'termine', 'annule') DEFAULT 'planifie' AFTER type");
        $this->ensureColumn("approval_status", "ENUM('pending', 'approved', 'rejected') DEFAULT 'approved' AFTER statut");
        $this->ensureColumn('approved_by', 'INT DEFAULT NULL AFTER approval_status');
        $this->ensureColumn('approved_at', 'DATETIME DEFAULT NULL AFTER approved_by');
        $this->ensureColumn('rejection_reason', 'TEXT DEFAULT NULL AFTER approved_at');

        $this->ensureIndex('idx_approval_status', 'approval_status');
    }

    /**
     * Add missing column if it does not exist.
     * @param string $name
     * @param string $definition
     * @return void
     */
    private function ensureColumn($name, $definition) {
        $hasColumn = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE '" . $name . "'")->fetch();
        if (!$hasColumn) {
            $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN {$name} {$definition}");
        }
    }

    /**
     * Add missing index if it does not exist.
     * @param string $indexName
     * @param string $columns
     * @return void
     */
    private function ensureIndex($indexName, $columns) {
        $hasIndex = $this->db->query("SHOW INDEX FROM {$this->table} WHERE Key_name = '" . $indexName . "'")->fetch();
        if (!$hasIndex) {
            $this->db->exec("ALTER TABLE {$this->table} ADD INDEX {$indexName} ({$columns})");
        }
    }

    /**
     * Create a new evenement
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table}
            (title, titre, description, date_debut, date_fin, heure_debut, heure_fin, lieu, capacite_max, type, statut, approval_status, location, event_date, created_by, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
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

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update an existing evenement
     * @param int $id
     * @param array $data
     * @return bool
     */
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

    /**
     * Get upcoming evenements with creator name
     * @return array
     */
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

    /**
     * Get most recent evenements
     * @param int $limit
     * @return array
     */
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

    /**
     * Get approved upcoming evenements (for front office).
     * @return array
     */
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

    /**
     * Find one evenement with creator identity.
     * @param int $id
     * @return array|false
     */
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

    /**
     * Get all evenements created by one teacher.
     * @param int $teacherId
     * @return array
     */
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

    /**
     * Find one evenement owned by the given creator.
     * @param int $id
     * @param int $creatorId
     * @return array|false
     */
    public function findByIdAndCreator($id, $creatorId) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND created_by = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $id, (int) $creatorId]);
        return $stmt->fetch();
    }

    /**
     * Get pending evenement requests proposed by teachers.
     * @return array
     */
    public function getPendingTeacherRequests() {
        $sql = "SELECT e.*, u.name as creator_name, u.email as creator_email
                FROM {$this->table} e
                JOIN users u ON u.id = e.created_by
                WHERE e.approval_status = 'pending' AND u.role = 'teacher'
                ORDER BY e.created_at DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get rejected evenement requests proposed by teachers.
     * @return array
     */
    public function getRejectedTeacherRequests() {
        $sql = "SELECT e.*, u.name as creator_name, u.email as creator_email
                FROM {$this->table} e
                JOIN users u ON u.id = e.created_by
                WHERE e.approval_status = 'rejected' AND u.role = 'teacher'
                ORDER BY e.updated_at DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Approve or reject one evenement request.
     * @param int $id
     * @param string $status approved|rejected
     * @param int|null $adminId
     * @param string|null $reason
     * @return bool
     */
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

    /**
     * Move event back to pending only when it was approved or rejected before.
     * @param int $id
     * @return bool
     */
    public function markPendingIfNotPending($id) {
        $sql = "UPDATE {$this->table}
                SET approval_status = 'pending',
                    approved_by = NULL,
                    approved_at = NULL,
                    rejection_reason = NULL,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND approval_status != 'pending'";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([(int) $id]);
    }
}
