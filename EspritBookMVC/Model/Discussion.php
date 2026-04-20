<?php

require_once __DIR__ . '/../Model/BaseModel.php';

class Discussion extends BaseModel
{
    protected string $table = 'discussion';
    private ?array $columns = null;

    private function columns(): array
    {
        if ($this->columns === null) {
            $this->columns = [];
            try {
                $stmt = $this->db->query("SHOW COLUMNS FROM {$this->table}");
                foreach ($stmt->fetchAll() as $row) {
                    $this->columns[] = (string) ($row['Field'] ?? '');
                }
            } catch (PDOException $e) {
                $this->columns = [];
            }
        }
        return $this->columns;
    }

    private function hasColumn(string $name): bool
    {
        return in_array($name, $this->columns(), true);
    }

    private function idCol(): string
    {
        return $this->hasColumn('id_discussion') ? 'id_discussion' : 'id';
    }

    private function groupCol(): string
    {
        return $this->hasColumn('id_groupe') ? 'id_groupe' : 'group_id';
    }

    private function authorCol(): string
    {
        return $this->hasColumn('id_auteur') ? 'id_auteur' : 'created_by';
    }

    private function dateCol(): string
    {
        return $this->hasColumn('date_creation') ? 'date_creation' : 'created_at';
    }

    private function titleCol(): string
    {
        return $this->hasColumn('titre') ? 'titre' : 'title';
    }

    private function contentCol(): string
    {
        return $this->hasColumn('contenu') ? 'contenu' : 'content';
    }

    private function approvalCol(): string
    {
        if ($this->hasColumn('approval_statut')) {
            return 'approval_statut';
        }
        if ($this->hasColumn('approval_status')) {
            return 'approval_status';
        }
        return '';
    }

    private function ensureApprovalSchema(): void
    {
        if ($this->approvalCol() !== '') {
            return;
        }
        try {
            $this->db->exec(
                "ALTER TABLE {$this->table} ADD COLUMN approval_statut VARCHAR(32) NOT NULL DEFAULT 'approuve'"
            );
            $this->columns = null;
        } catch (Throwable $e) {
            // ignore
        }
    }

    /**
     * All discussions in a group visible to this user: approved for everyone,
     * or non-approved only for author and group creator.
     */
    public function getByGroupForViewer(int $groupId, int $viewerUserId, int $groupCreatorId): array
    {
        $this->ensureApprovalSchema();
        $approvalCol = $this->approvalCol();
        if ($approvalCol === '') {
            return $this->getByGroup($groupId);
        }

        $groupCol = $this->groupCol();
        $authorCol = $this->authorCol();
        $dateCol = $this->dateCol();
        $titleCol = $this->titleCol();
        $contentCol = $this->contentCol();

        try {
            $stmt = $this->db->prepare(
                "SELECT d.*, d.{$titleCol} AS titre, d.{$contentCol} AS contenu, u.name AS auteur_name
                 FROM {$this->table} d
                 LEFT JOIN users u ON u.id = d.{$authorCol}
                 WHERE d.{$groupCol} = ?
                 AND (
                    d.{$approvalCol} = 'approuve'
                    OR d.{$authorCol} = ?
                    OR ? = ?
                 )
                 ORDER BY d.{$dateCol} DESC"
            );
            $stmt->execute([$groupId, $viewerUserId, $viewerUserId, $groupCreatorId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getByGroup(int $groupId): array
    {
        $groupCol = $this->groupCol();
        $authorCol = $this->authorCol();
        $dateCol = $this->dateCol();
        $titleCol = $this->titleCol();
        $contentCol = $this->contentCol();

        try {
            $stmt = $this->db->prepare(
                "SELECT d.*, d.{$titleCol} AS titre, d.{$contentCol} AS contenu, u.name AS auteur_name
                 FROM {$this->table} d
                 LEFT JOIN users u ON u.id = d.{$authorCol}
                 WHERE d.{$groupCol} = ?
                 ORDER BY d.{$dateCol} DESC"
            );
            $stmt->execute([$groupId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getByAuthor(int $authorId): array
    {
        $this->ensureApprovalSchema();
        $idCol = $this->idCol();
        $groupCol = $this->groupCol();
        $authorCol = $this->authorCol();
        $dateCol = $this->dateCol();
        $titleCol = $this->titleCol();
        $contentCol = $this->contentCol();

        try {
            $stmt = $this->db->prepare(
                "SELECT d.*, d.{$idCol} AS id_discussion, d.{$titleCol} AS titre, d.{$contentCol} AS contenu, g.nom_groupe
                 FROM {$this->table} d
                 LEFT JOIN groupe g ON g.id_groupe = d.{$groupCol}
                 WHERE d.{$authorCol} = ?
                 ORDER BY d.{$dateCol} DESC"
            );
            $stmt->execute([$authorId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function findOwnedBy(int $discussionId, int $authorId): ?array
    {
        $idCol = $this->idCol();
        $groupCol = $this->groupCol();
        $authorCol = $this->authorCol();
        $dateCol = $this->dateCol();
        $titleCol = $this->titleCol();
        $contentCol = $this->contentCol();

        try {
            $stmt = $this->db->prepare(
                "SELECT d.*, d.{$idCol} AS id_discussion, d.{$titleCol} AS titre, d.{$contentCol} AS contenu, d.{$groupCol} AS id_groupe, d.{$dateCol} AS date_creation
                 FROM {$this->table} d
                 WHERE d.{$idCol} = ? AND d.{$authorCol} = ?
                 LIMIT 1"
            );
            $stmt->execute([$discussionId, $authorId]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function createForGroup(int $groupId, int $authorId, string $title, string $content): bool
    {
        $this->ensureApprovalSchema();
        $groupCol = $this->groupCol();
        $authorCol = $this->authorCol();
        $dateCol = $this->dateCol();
        $titleCol = $this->titleCol();
        $contentCol = $this->contentCol();
        $approvalCol = $this->approvalCol();

        try {
            if ($approvalCol !== '') {
                $stmt = $this->db->prepare(
                    "INSERT INTO {$this->table} ({$titleCol}, {$contentCol}, {$dateCol}, {$groupCol}, {$authorCol}, {$approvalCol})
                     VALUES (?, ?, NOW(), ?, ?, 'en_cours')"
                );
                return $stmt->execute([$title, $content, $groupId, $authorId]);
            }
            $stmt = $this->db->prepare(
                "INSERT INTO {$this->table} ({$titleCol}, {$contentCol}, {$dateCol}, {$groupCol}, {$authorCol})
                 VALUES (?, ?, NOW(), ?, ?)"
            );
            return $stmt->execute([$title, $content, $groupId, $authorId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateOwned(int $discussionId, int $authorId, string $title, string $content, int $groupId): bool
    {
        $this->ensureApprovalSchema();
        $idCol = $this->idCol();
        $groupCol = $this->groupCol();
        $authorCol = $this->authorCol();
        $titleCol = $this->titleCol();
        $contentCol = $this->contentCol();
        $approvalCol = $this->approvalCol();

        try {
            if ($approvalCol !== '') {
                $stmt = $this->db->prepare(
                    "UPDATE {$this->table}
                     SET {$titleCol} = ?, {$contentCol} = ?, {$groupCol} = ?, {$approvalCol} = 'en_cours'
                     WHERE {$idCol} = ? AND {$authorCol} = ?"
                );
                return $stmt->execute([$title, $content, $groupId, $discussionId, $authorId]);
            }
            $stmt = $this->db->prepare(
                "UPDATE {$this->table}
                 SET {$titleCol} = ?, {$contentCol} = ?, {$groupCol} = ?
                 WHERE {$idCol} = ? AND {$authorCol} = ?"
            );
            return $stmt->execute([$title, $content, $groupId, $discussionId, $authorId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAllForAdmin(int $limit = 200): array
    {
        $this->ensureApprovalSchema();
        $groupCol = $this->groupCol();
        $authorCol = $this->authorCol();
        $dateCol = $this->dateCol();

        try {
            $stmt = $this->db->prepare(
                "SELECT d.*, g.nom_groupe, u.name AS auteur_name
                 FROM {$this->table} d
                 LEFT JOIN groupe g ON g.id_groupe = d.{$groupCol}
                 LEFT JOIN users u ON u.id = d.{$authorCol}
                 ORDER BY d.{$dateCol} DESC
                 LIMIT ?"
            );
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function setApprovalStatus(int $discussionId, string $status): bool
    {
        if (!in_array($status, ['approuve', 'rejete'], true)) {
            return false;
        }
        $this->ensureApprovalSchema();
        $col = $this->approvalCol();
        if ($col === '') {
            return false;
        }
        $idCol = $this->idCol();
        try {
            $stmt = $this->db->prepare(
                "UPDATE {$this->table} SET {$col} = ? WHERE {$idCol} = ?"
            );
            return $stmt->execute([$status, $discussionId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteOwned(int $discussionId, int $authorId): bool
    {
        $idCol = $this->idCol();
        $authorCol = $this->authorCol();
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$idCol} = ? AND {$authorCol} = ?");
            return $stmt->execute([$discussionId, $authorId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getRowByPk(int $discussionId): ?array
    {
        $idCol = $this->idCol();
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$idCol} = ? LIMIT 1");
            $stmt->execute([$discussionId]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function deleteByPrimaryKey(int $discussionId): bool
    {
        $idCol = $this->idCol();
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$idCol} = ?");
            return $stmt->execute([$discussionId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteAllForGroup(int $groupId): bool
    {
        $groupCol = $this->groupCol();
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$groupCol} = ?");
            return $stmt->execute([$groupId]);
        } catch (PDOException $e) {
            return false;
        }
    }
}

