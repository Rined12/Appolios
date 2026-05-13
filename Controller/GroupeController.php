<?php

require_once __DIR__ . '/../config/database.php';

class GroupeController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getConnection();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM groupe WHERE id_groupe = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function fetchAllApproved(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM groupe WHERE approval_statut = 'approuve' ORDER BY date_creation DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function fetchAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM groupe ORDER BY date_creation DESC');
        return $stmt->fetchAll();
    }

    public function fetchByCreator(int $creatorId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM groupe WHERE id_createur = ? ORDER BY date_creation DESC');
        $stmt->execute([$creatorId]);
        return $stmt->fetchAll();
    }

    public function create(array $data)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO groupe (nom_groupe, description, date_creation, id_createur, statut, approval_statut, image_url)
             VALUES (?, ?, NOW(), ?, ?, ?, ?)'
        );
        $ok = $stmt->execute([
            (string) ($data['nom_groupe'] ?? ''),
            (string) ($data['description'] ?? ''),
            (int) ($data['id_createur'] ?? 0),
            (string) ($data['statut'] ?? 'actif'),
            (string) ($data['approval_statut'] ?? 'en_cours'),
            ($data['image_url'] ?? null) !== '' ? $data['image_url'] : null,
        ]);

        if (!$ok) {
            return false;
        }
        return (int) $this->db->lastInsertId();
    }

    public function updateGroupe(int $idGroupe, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE groupe
             SET nom_groupe = ?, description = ?, statut = ?, approval_statut = ?, image_url = ?
             WHERE id_groupe = ?'
        );
        return $stmt->execute([
            (string) ($data['nom_groupe'] ?? ''),
            (string) ($data['description'] ?? ''),
            (string) ($data['statut'] ?? 'actif'),
            (string) ($data['approval_statut'] ?? 'en_cours'),
            ($data['image_url'] ?? null) !== '' ? $data['image_url'] : null,
            $idGroupe,
        ]);
    }

    public function deleteGroupe(int $idGroupe): bool
    {
        $stmt = $this->db->prepare('DELETE FROM groupe WHERE id_groupe = ?');
        return $stmt->execute([$idGroupe]);
    }

    public function estMembre(int $id_groupe, int $id_user): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM groupe_user WHERE id_groupe = ? AND id_user = ?');
        $stmt->execute([$id_groupe, $id_user]);
        return (bool) $stmt->fetchColumn();
    }

    public function ajouterMembre(int $id_groupe, int $id_user, string $role = 'membre'): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO groupe_user (id_groupe, id_user, role, date_adhesion)
             VALUES (?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE role = VALUES(role)'
        );
        return $stmt->execute([$id_groupe, $id_user, $role]);
    }

    public function retirerMembre(int $id_groupe, int $id_user): bool
    {
        $stmt = $this->db->prepare('DELETE FROM groupe_user WHERE id_groupe = ? AND id_user = ?');
        return $stmt->execute([$id_groupe, $id_user]);
    }

    public function fetchMembres(int $id_groupe): array
    {
        $stmt = $this->db->prepare(
            'SELECT gu.*, u.name, u.email
             FROM groupe_user gu
             JOIN users u ON u.id = gu.id_user
             WHERE gu.id_groupe = ?
             ORDER BY gu.date_adhesion DESC'
        );
        $stmt->execute([$id_groupe]);
        return $stmt->fetchAll();
    }
}
