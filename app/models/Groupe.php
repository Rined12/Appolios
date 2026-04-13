<?php
/**
 * APPOLIOS — Social Learning
 * Model : Groupe
 */

require_once __DIR__ . '/../core/Model.php';

class Groupe extends Model {
    protected $table = 'groupe';

    /** @var bool|null Cache : colonne approval_statut présente sur `groupe` */
    private static ?bool $schemaHasApproval = null;

    private function hasApprovalColumn(): bool {
        if (self::$schemaHasApproval !== null) {
            return self::$schemaHasApproval;
        }
        try {
            $r = $this->db->query("SHOW COLUMNS FROM `groupe` LIKE 'approval_statut'");
            self::$schemaHasApproval = ($r && $r->fetch(PDO::FETCH_ASSOC) !== false);
        } catch (\Throwable $e) {
            self::$schemaHasApproval = false;
        }
        return self::$schemaHasApproval;
    }

    // ------------------------------------------------------------------
    // READ
    // ------------------------------------------------------------------

    /**
     * All groups with creator name — paginated (admin : tous les statuts d'approbation).
     */
    public function getAllWithCreator(int $limit = 10, int $offset = 0): array {
        $sql = "SELECT g.*, u.name AS nom_createur
                FROM groupe g
                JOIN users u ON u.id = g.id_createur
                ORDER BY g.date_creation DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Groupes visibles publiquement (approuvés par l'admin).
     */
    public function getAllWithCreatorPublic(int $limit = 10, int $offset = 0): array {
        if (!$this->hasApprovalColumn()) {
            return $this->getAllWithCreator($limit, $offset);
        }
        $sql = "SELECT g.*, u.name AS nom_createur
                FROM groupe g
                JOIN users u ON u.id = g.id_createur
                WHERE g.approval_statut = 'approuve'
                ORDER BY g.date_creation DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Total count of all groups.
     */
    public function countAll(): int {
        $stmt = $this->db->query("SELECT COUNT(*) AS cnt FROM groupe");
        return (int) $stmt->fetchColumn();
    }

    public function countApproved(): int {
        if (!$this->hasApprovalColumn()) {
            return $this->countAll();
        }
        $stmt = $this->db->query("SELECT COUNT(*) FROM groupe WHERE approval_statut = 'approuve'");
        return (int) $stmt->fetchColumn();
    }

    public function countPendingApproval(): int {
        if (!$this->hasApprovalColumn()) {
            return 0;
        }
        $stmt = $this->db->query("SELECT COUNT(*) FROM groupe WHERE approval_statut = 'en_attente'");
        return (int) $stmt->fetchColumn();
    }

    /** Ligne groupe seule (sans jointure). */
    public function findById(int|string $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM groupe WHERE id_groupe = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Find group by id with creator info.
     */
    public function findByIdWithCreator(int $id): ?array {
        $sql = "SELECT g.*, u.name AS nom_createur
                FROM groupe g
                JOIN users u ON u.id = g.id_createur
                WHERE g.id_groupe = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Groups a specific user belongs to.
     */
    public function getGroupesForUser(int $userId, int $limit = 10, int $offset = 0): array {
        $sql = "SELECT g.*, u.name AS nom_createur, gu.role AS mon_role
                FROM groupe g
                JOIN users u ON u.id = g.id_createur
                JOIN groupe_user gu ON gu.id_groupe = g.id_groupe
                WHERE gu.id_user = ?
                ORDER BY g.date_creation DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countGroupesForUser(int $userId): int {
        $sql = "SELECT COUNT(*) FROM groupe_user WHERE id_user = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    // ------------------------------------------------------------------
    // CREATE / UPDATE / DELETE
    // ------------------------------------------------------------------

    /**
     * @param string $approvalStatut en_attente | approuve | refuse (défaut : en attente jusqu’à validation admin)
     */
    public function create(string $nom, string $description, int $idCreateur, string $approvalStatut = 'en_attente'): int|false {
        if (!in_array($approvalStatut, ['en_attente', 'approuve', 'refuse'], true)) {
            $approvalStatut = 'en_attente';
        }
        if ($this->hasApprovalColumn()) {
            $sql = "INSERT INTO groupe (nom_groupe, description, date_creation, statut, approval_statut, id_createur)
                    VALUES (?, ?, NOW(), 'actif', ?, ?)";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([$nom, $description, $approvalStatut, $idCreateur]);
        } else {
            $sql = "INSERT INTO groupe (nom_groupe, description, date_creation, statut, id_createur)
                    VALUES (?, ?, NOW(), 'actif', ?)";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([$nom, $description, $idCreateur]);
        }
        if ($ok) {
            $newId = (int) $this->db->lastInsertId();
            $this->addMember($newId, $idCreateur, 'admin');
            return $newId;
        }
        return false;
    }

    public function setApprovalStatut(int $id, string $status): bool {
        if (!$this->hasApprovalColumn()) {
            return false;
        }
        if (!in_array($status, ['en_attente', 'approuve', 'refuse'], true)) {
            return false;
        }
        $stmt = $this->db->prepare("UPDATE groupe SET approval_statut = ? WHERE id_groupe = ?");
        return $stmt->execute([$status, $id]);
    }

    public function update(int $id, string $nom, string $description, string $statut): bool {
        $sql = "UPDATE groupe SET nom_groupe = ?, description = ?, statut = ? WHERE id_groupe = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nom, $description, $statut, $id]);
    }

    public function deleteById(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM groupe WHERE id_groupe = ?");
        return $stmt->execute([$id]);
    }

    // ------------------------------------------------------------------
    // MEMBERSHIP
    // ------------------------------------------------------------------

    public function isMember(int $idGroupe, int $idUser): bool {
        $sql = "SELECT COUNT(*) FROM groupe_user WHERE id_groupe = ? AND id_user = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idGroupe, $idUser]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function getMemberRole(int $idGroupe, int $idUser): ?string {
        $sql = "SELECT role FROM groupe_user WHERE id_groupe = ? AND id_user = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idGroupe, $idUser]);
        $row = $stmt->fetch();
        return $row ? $row['role'] : null;
    }

    public function isMemberAdmin(int $idGroupe, int $idUser): bool {
        return $this->getMemberRole($idGroupe, $idUser) === 'admin';
    }

    public function addMember(int $idGroupe, int $idUser, string $role = 'membre'): bool {
        $sql = "INSERT IGNORE INTO groupe_user (id_groupe, id_user, role, date_adhesion)
                VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idGroupe, $idUser, $role]);
    }

    public function removeMember(int $idGroupe, int $idUser): bool {
        $sql = "DELETE FROM groupe_user WHERE id_groupe = ? AND id_user = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idGroupe, $idUser]);
    }

    public function getMembers(int $idGroupe): array {
        $sql = "SELECT u.id, u.name, u.email, gu.role, gu.date_adhesion
                FROM groupe_user gu
                JOIN users u ON u.id = gu.id_user
                WHERE gu.id_groupe = ?
                ORDER BY gu.date_adhesion ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idGroupe]);
        return $stmt->fetchAll();
    }

    public function countMembers(int $idGroupe): int {
        $sql = "SELECT COUNT(*) FROM groupe_user WHERE id_groupe = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idGroupe]);
        return (int) $stmt->fetchColumn();
    }

    // ------------------------------------------------------------------
    // STATS (for admin dashboard)
    // ------------------------------------------------------------------

    public function countActif(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM groupe WHERE statut = 'actif'");
        return (int) $stmt->fetchColumn();
    }

    public function countArchive(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM groupe WHERE statut = 'archivé'");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Liste avec nom du créateur (routes social-learning/* catalogue).
     *
     * @param bool $onlyApproved true = uniquement groupes approuvés par l’admin
     */
    public function paginateWithCreator(int $limit, int $offset, bool $onlyApproved = true): array {
        $sql = 'SELECT g.*, u.name AS nom_createur
                FROM groupe g
                LEFT JOIN users u ON u.id = g.id_createur';
        if ($onlyApproved && $this->hasApprovalColumn()) {
            $sql .= " WHERE g.approval_statut = 'approuve'";
        }
        $sql .= ' ORDER BY g.date_creation DESC LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existsApproved(int $id): bool {
        if (!$this->hasApprovalColumn()) {
            return $this->findById($id) !== null;
        }
        $stmt = $this->db->prepare("SELECT 1 FROM groupe WHERE id_groupe = ? AND approval_statut = 'approuve' LIMIT 1");
        $stmt->execute([$id]);
        return (bool) $stmt->fetchColumn();
    }
}
