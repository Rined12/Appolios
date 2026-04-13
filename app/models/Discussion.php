<?php
/**
 * APPOLIOS — Social Learning
 * Model : Discussion
 */

require_once __DIR__ . '/../core/Model.php';

class Discussion extends Model {
    protected $table = 'discussion';

    /** @var bool|null Cache : colonne approval_statut présente sur `discussion` */
    private static ?bool $schemaHasApproval = null;
    /** @var bool|null Cache : colonne approval_statut présente sur `groupe` */
    private static ?bool $schemaGroupeHasApproval = null;

    private function hasApprovalColumn(): bool {
        if (self::$schemaHasApproval !== null) {
            return self::$schemaHasApproval;
        }
        try {
            $r = $this->db->query("SHOW COLUMNS FROM `discussion` LIKE 'approval_statut'");
            self::$schemaHasApproval = ($r && $r->fetch(PDO::FETCH_ASSOC) !== false);
        } catch (\Throwable $e) {
            self::$schemaHasApproval = false;
        }
        return self::$schemaHasApproval;
    }

    private function hasGroupeApprovalColumn(): bool {
        if (self::$schemaGroupeHasApproval !== null) {
            return self::$schemaGroupeHasApproval;
        }
        try {
            $r = $this->db->query("SHOW COLUMNS FROM `groupe` LIKE 'approval_statut'");
            self::$schemaGroupeHasApproval = ($r && $r->fetch(PDO::FETCH_ASSOC) !== false);
        } catch (\Throwable $e) {
            self::$schemaGroupeHasApproval = false;
        }
        return self::$schemaGroupeHasApproval;
    }

    /** Ligne discussion seule (sans jointure). */
    public function findById(int|string $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM discussion WHERE id_discussion = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Discussion + infos groupe (pour autorisations messages, etc.). */
    public function findWithGroupe(int $id): ?array {
        $gCol = $this->hasGroupeApprovalColumn()
            ? ', g.approval_statut AS groupe_approval_statut'
            : '';
        $sql = "SELECT d.*, g.nom_groupe, g.id_createur AS groupe_id_createur{$gCol}
                FROM discussion d
                INNER JOIN groupe g ON g.id_groupe = d.id_groupe
                WHERE d.id_discussion = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // ------------------------------------------------------------------
    // READ
    // ------------------------------------------------------------------

    /**
     * Discussions for a group — paginated — with author name & message count.
     */
    public function getByGroupe(int $idGroupe, int $limit = 10, int $offset = 0): array {
        $approvalWhere = $this->hasApprovalColumn() ? ' AND d.approval_statut = \'approuve\'' : '';
        $sql = "SELECT d.*, u.name AS nom_auteur,
                       (SELECT COUNT(*) FROM message m WHERE m.id_discussion = d.id_discussion) AS nb_messages
                FROM discussion d
                JOIN users u ON u.id = d.id_auteur
                WHERE d.id_groupe = ?{$approvalWhere}
                ORDER BY d.date_creation DESC
                LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $idGroupe, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countByGroupe(int $idGroupe): int {
        $extra = $this->hasApprovalColumn() ? " AND approval_statut = 'approuve'" : '';
        $sql = "SELECT COUNT(*) FROM discussion WHERE id_groupe = ?{$extra}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idGroupe]);
        return (int) $stmt->fetchColumn();
    }

    public function countPendingApproval(): int {
        if (!$this->hasApprovalColumn()) {
            return 0;
        }
        $stmt = $this->db->query("SELECT COUNT(*) FROM discussion WHERE approval_statut = 'en_attente'");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Single discussion with author name.
     */
    public function findByIdWithAuthor(int $id): ?array {
        $sql = "SELECT d.*, u.name AS nom_auteur
                FROM discussion d
                JOIN users u ON u.id = d.id_auteur
                WHERE d.id_discussion = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * All discussions — for admin — paginated.
     */
    public function getAllWithDetails(int $limit = 10, int $offset = 0): array {
        $sql = "SELECT d.*, u.name AS nom_auteur, g.nom_groupe
                FROM discussion d
                JOIN users u ON u.id = d.id_auteur
                JOIN groupe g ON g.id_groupe = d.id_groupe
                ORDER BY d.date_creation DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM discussion");
        return (int) $stmt->fetchColumn();
    }

    // ------------------------------------------------------------------
    // CREATE / UPDATE / DELETE
    // ------------------------------------------------------------------

    public function create(string $titre, string $contenu, int $idGroupe, int $idAuteur, string $approvalStatut = 'en_attente'): int|false {
        if (!in_array($approvalStatut, ['en_attente', 'approuve', 'refuse'], true)) {
            $approvalStatut = 'en_attente';
        }
        if ($this->hasApprovalColumn()) {
            $sql = "INSERT INTO discussion (titre, contenu, date_creation, nb_likes, id_groupe, id_auteur, approval_statut)
                    VALUES (?, ?, NOW(), 0, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([$titre, $contenu, $idGroupe, $idAuteur, $approvalStatut]);
        } else {
            $sql = "INSERT INTO discussion (titre, contenu, date_creation, nb_likes, id_groupe, id_auteur)
                    VALUES (?, ?, NOW(), 0, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([$titre, $contenu, $idGroupe, $idAuteur]);
        }
        if ($ok) {
            return (int) $this->db->lastInsertId();
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
        $stmt = $this->db->prepare("UPDATE discussion SET approval_statut = ? WHERE id_discussion = ?");
        return $stmt->execute([$status, $id]);
    }

    public function update(int $id, string $titre, string $contenu): bool {
        $sql = "UPDATE discussion SET titre = ?, contenu = ? WHERE id_discussion = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$titre, $contenu, $id]);
    }

    public function deleteById(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM discussion WHERE id_discussion = ?");
        return $stmt->execute([$id]);
    }

    public function incrementLikes(int $id): bool {
        $sql = "UPDATE discussion SET nb_likes = nb_likes + 1 WHERE id_discussion = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Fil global (discussion + groupe approuvés) — routes social-learning/discussion.
     */
    public function paginateWithGroupe(int $limit, int $offset): array {
        $w = [];
        if ($this->hasApprovalColumn()) {
            $w[] = "d.approval_statut = 'approuve'";
        }
        if ($this->hasGroupeApprovalColumn()) {
            $w[] = "g.approval_statut = 'approuve'";
        }
        $where = $w ? ('WHERE ' . implode(' AND ', $w)) : '';
        $sql = "SELECT d.*, g.nom_groupe
                FROM discussion d
                INNER JOIN groupe g ON g.id_groupe = d.id_groupe
                {$where}
                ORDER BY d.date_creation DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countVisibleFeed(): int {
        $w = [];
        if ($this->hasApprovalColumn()) {
            $w[] = "d.approval_statut = 'approuve'";
        }
        if ($this->hasGroupeApprovalColumn()) {
            $w[] = "g.approval_statut = 'approuve'";
        }
        $where = $w ? ('WHERE ' . implode(' AND ', $w)) : '';
        $sql = "SELECT COUNT(*) FROM discussion d
                INNER JOIN groupe g ON g.id_groupe = d.id_groupe
                {$where}";
        return (int) $this->db->query($sql)->fetchColumn();
    }
}
