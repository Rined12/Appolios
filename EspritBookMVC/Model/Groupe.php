<?php

require_once __DIR__ . '/../Model/BaseModel.php';

class Groupe extends BaseModel
{
    protected string $table = 'groupe';
    private ?array $columnsCache = null;

    private function getColumns(): array
    {
        if ($this->columnsCache !== null) {
            return $this->columnsCache;
        }

        $stmt = $this->db->query("SHOW COLUMNS FROM {$this->table}");
        $cols = [];
        foreach ($stmt->fetchAll() as $row) {
            $cols[] = (string) ($row['Field'] ?? '');
        }
        $this->columnsCache = $cols;
        return $cols;
    }

    private function hasColumn(string $name): bool
    {
        return in_array($name, $this->getColumns(), true);
    }

    private function approvalColumn(): string
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
        if ($this->approvalColumn() !== '') {
            return;
        }
        try {
            $this->db->exec(
                "ALTER TABLE {$this->table} ADD COLUMN approval_statut VARCHAR(32) NOT NULL DEFAULT 'en_cours'"
            );
            $this->columnsCache = null;
        } catch (Throwable $e) {
        }
    }

    private function statutColumn(): string
    {
        if ($this->hasColumn('statut')) {
            return 'statut';
        }
        if ($this->hasColumn('status')) {
            return 'status';
        }
        return '';
    }

    private function creatorColumn(): string
    {
        if ($this->hasColumn('id_createur')) {
            return 'id_createur';
        }
        if ($this->hasColumn('created_by')) {
            return 'created_by';
        }
        return 'id_createur';
    }

    private function imageColumn(): string
    {
        foreach (['image_url', 'photo', 'image'] as $c) {
            if ($this->hasColumn($c)) {
                return $c;
            }
        }
        return '';
    }

    private function ensureImageSchema(): void
    {
        if ($this->imageColumn() !== '') {
            return;
        }
        try {
            $this->db->exec('ALTER TABLE groupe ADD COLUMN image_url VARCHAR(500) NULL DEFAULT NULL');
            $this->columnsCache = null;
        } catch (Throwable $e) {
        }
    }

    public function supportsStoredImage(): bool
    {
        $this->ensureImageSchema();
        return $this->imageColumn() !== '';
    }

    public function findById($id)
    {
        $this->ensureApprovalSchema();
        $approvalCol = $this->approvalColumn();
        $statutCol = $this->statutColumn();
        $creatorCol = $this->creatorColumn();
        $approvalSelect = $approvalCol !== '' ? "g.{$approvalCol} AS approval_statut" : "'en_cours' AS approval_statut";
        $statutSelect = $statutCol !== '' ? "g.{$statutCol} AS statut" : "'actif' AS statut";

        $stmt = $this->db->prepare(
            "SELECT
                g.*,
                {$approvalSelect},
                {$statutSelect},
                u.name AS createur_name
             FROM groupe g
             LEFT JOIN users u ON u.id = g.{$creatorCol}
             WHERE g.id_groupe = ?"
        );
        $stmt->execute([(int) $id]);
        return $stmt->fetch();
    }

    public function getAllWithCreator($limit = 20, $offset = 0): array
    {
        $this->ensureApprovalSchema();
        $approvalCol = $this->approvalColumn();
        $statutCol = $this->statutColumn();
        $creatorCol = $this->creatorColumn();
        $approvalSelect = $approvalCol !== '' ? "g.{$approvalCol} AS approval_statut" : "'en_cours' AS approval_statut";
        $statutSelect = $statutCol !== '' ? "g.{$statutCol} AS statut" : "'actif' AS statut";

        $stmt = $this->db->prepare(
            "SELECT
                g.*,
                {$approvalSelect},
                {$statutSelect},
                u.name AS createur_name
             FROM groupe g
             LEFT JOIN users u ON u.id = g.{$creatorCol}
             ORDER BY g.date_creation DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->bindValue(1, (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllWithCreatorPublic($limit = 20, $offset = 0): array
    {
        $this->ensureApprovalSchema();
        $approvalCol = $this->approvalColumn();
        $statutCol = $this->statutColumn();
        $creatorCol = $this->creatorColumn();
        $approvalSelect = $approvalCol !== '' ? "g.{$approvalCol} AS approval_statut" : "'en_cours' AS approval_statut";
        $statutSelect = $statutCol !== '' ? "g.{$statutCol} AS statut" : "'actif' AS statut";
        if ($approvalCol === '') {
            return [];
        }
        $approvalWhere = "g.{$approvalCol} = 'approuve'";

        $stmt = $this->db->prepare(
            "SELECT
                g.*,
                {$approvalSelect},
                {$statutSelect},
                u.name AS createur_name
             FROM groupe g
             LEFT JOIN users u ON u.id = g.{$creatorCol}
             WHERE {$approvalWhere}
             ORDER BY g.date_creation DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->bindValue(1, (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $data)
    {
        $this->ensureImageSchema();
        $this->ensureApprovalSchema();
        $approvalCol = $this->approvalColumn();
        $statutCol = $this->statutColumn();
        $creatorCol = $this->creatorColumn();
        $imgCol = $this->imageColumn();

        $columns = ['nom_groupe', 'description', 'date_creation', $creatorCol];
        $values = ['?', '?', 'NOW()', '?'];
        $params = [
            $data['nom_groupe'],
            $data['description'],
            (int) $data['id_createur'],
        ];

        if ($statutCol !== '') {
            $columns[] = $statutCol;
            $values[] = '?';
            $params[] = $data['statut'];
        }

        if ($approvalCol !== '') {
            $columns[] = $approvalCol;
            $values[] = '?';
            $params[] = $data['approval_statut'];
        }

        if ($imgCol !== '' && !empty($data['image_url'])) {
            $columns[] = $imgCol;
            $values[] = '?';
            $params[] = $data['image_url'];
        }

        $stmt = $this->db->prepare(
            "INSERT INTO groupe (" . implode(', ', $columns) . ")
             VALUES (" . implode(', ', $values) . ")"
        );
        $ok = $stmt->execute($params);
        if (!$ok) {
            return false;
        }

        return (int) $this->db->lastInsertId();
    }

    public function updateGroupe(int $idGroupe, array $data): bool
    {
        $this->ensureImageSchema();
        $this->ensureApprovalSchema();
        $approvalCol = $this->approvalColumn();
        $statutCol = $this->statutColumn();
        $imgCol = $this->imageColumn();
        $setParts = ["nom_groupe = ?", "description = ?"];
        $params = [$data['nom_groupe'], $data['description']];
        if ($statutCol !== '') {
            $setParts[] = "{$statutCol} = ?";
            $params[] = $data['statut'];
        }
        if ($approvalCol !== '') {
            $setParts[] = "{$approvalCol} = ?";
            $params[] = $data['approval_statut'];
        }
        if ($imgCol !== '' && array_key_exists('image_url', $data)) {
            $setParts[] = "{$imgCol} = ?";
            $params[] = $data['image_url'];
        }
        $params[] = $idGroupe;

        $stmt = $this->db->prepare(
            "UPDATE groupe
             SET " . implode(', ', $setParts) . "
             WHERE id_groupe = ?"
        );
        return $stmt->execute($params);
    }

    public function delete($id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM groupe WHERE id_groupe = ?");
        return $stmt->execute([(int) $id]);
    }

    public function deleteMembresForGroup(int $idGroupe): bool
    {
        $stmt = $this->db->prepare('DELETE FROM groupe_user WHERE id_groupe = ?');
        return $stmt->execute([(int) $idGroupe]);
    }

    public function estMembre($id_groupe, $id_user): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM groupe_user WHERE id_groupe = ? AND id_user = ?");
        $stmt->execute([(int) $id_groupe, (int) $id_user]);
        return (bool) $stmt->fetchColumn();
    }

    public function ajouterMembre($id_groupe, $id_user, $role = 'membre'): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO groupe_user (id_groupe, id_user, role, date_adhesion)
             VALUES (?, ?, ?, NOW())"
        );
        return $stmt->execute([(int) $id_groupe, (int) $id_user, $role]);
    }

    public function getMembres($id_groupe): array
    {
        $stmt = $this->db->prepare(
            "SELECT gu.*, u.name, u.email
             FROM groupe_user gu
             JOIN users u ON u.id = gu.id_user
             WHERE gu.id_groupe = ?
             ORDER BY gu.date_adhesion DESC"
        );
        $stmt->execute([(int) $id_groupe]);
        return $stmt->fetchAll();
    }

    public function getByCreator(int $idCreateur): array
    {
        $this->ensureApprovalSchema();
        $creatorCol = $this->creatorColumn();
        $stmt = $this->db->prepare(
            "SELECT g.*, u.name AS createur_name
             FROM groupe g
             LEFT JOIN users u ON u.id = g.{$creatorCol}
             WHERE g.{$creatorCol} = ?
             ORDER BY g.date_creation DESC"
        );
        $stmt->execute([$idCreateur]);
        return $stmt->fetchAll();
    }
}
