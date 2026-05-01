<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseRepository.php';

/**
 * Persistence for evenements. Domain fields live in EvenementEntity; rows stay arrays here.
 */
class EvenementRepository extends BaseRepository
{
    protected string $table = 'evenements';

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM evenements')->fetchColumn();
    }

    public function findRecent(int $limit): array
    {
        $st = $this->db->prepare(
            'SELECT * FROM evenements
             ORDER BY COALESCE(CONCAT(date_debut,\' \',heure_debut), event_date) ASC
             LIMIT ?'
        );
        $st->bindValue(1, $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public function findAllWithCreatorAndResourceCount(): array
    {
        return $this->db->query(
            'SELECT e.*, u.name as creator_name, u.role as creator_role, COUNT(r.id) as resource_count
             FROM evenements e
             JOIN users u ON e.created_by = u.id
             LEFT JOIN evenement_ressources r ON r.evenement_id = e.id
             GROUP BY e.id
             ORDER BY COALESCE(CONCAT(e.date_debut,\' \',e.heure_debut), e.event_date) ASC'
        )->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $st = $this->db->prepare('SELECT * FROM evenements WHERE id = ? LIMIT 1');
        $st->execute([$id]);
        return $st->fetch();
    }

    /**
     * @param array<string, mixed> $d
     */
    public function create(array $d): int|false
    {
        try {
            $st = $this->db->prepare(
                'INSERT INTO evenements
                 (title,titre,description,date_debut,date_fin,heure_debut,heure_fin,
                  lieu,capacite_max,type,statut,approval_status,location,event_date,created_by,created_at)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())'
            );
            $st->execute([
                $d['title'],
                $d['titre'],
                $d['description'],
                $d['date_debut'],
                $d['date_fin'],
                $d['heure_debut'],
                $d['heure_fin'],
                $d['lieu'],
                $d['capacite_max'],
                $d['type'],
                $d['statut'],
                $d['approval_status'] ?? 'approved',
                $d['location'],
                $d['event_date'],
                $d['created_by'],
            ]);

            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * @param array<string, mixed> $d
     */
    public function update(int $id, array $d): bool
    {
        $st = $this->db->prepare(
            'UPDATE evenements
             SET title=?,titre=?,description=?,date_debut=?,date_fin=?,
                 heure_debut=?,heure_fin=?,lieu=?,capacite_max=?,type=?,
                 statut=?,location=?,event_date=?,updated_at=CURRENT_TIMESTAMP
             WHERE id=?'
        );

        return $st->execute([
            $d['title'],
            $d['titre'],
            $d['description'],
            $d['date_debut'],
            $d['date_fin'],
            $d['heure_debut'],
            $d['heure_fin'],
            $d['lieu'],
            $d['capacite_max'],
            $d['type'],
            $d['statut'],
            $d['location'],
            $d['event_date'],
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $st = $this->db->prepare('DELETE FROM evenements WHERE id=?');

        return $st->execute([$id]);
    }

    public function findPendingTeacherRequests(): array
    {
        return $this->db->query(
            'SELECT e.*,u.name as creator_name,u.email as creator_email
             FROM evenements e JOIN users u ON u.id=e.created_by
             WHERE e.approval_status=\'pending\' AND u.role=\'teacher\'
             ORDER BY e.created_at DESC'
        )->fetchAll();
    }

    public function findRejectedTeacherRequests(): array
    {
        return $this->db->query(
            'SELECT e.*,u.name as creator_name,u.email as creator_email
             FROM evenements e JOIN users u ON u.id=e.created_by
             WHERE e.approval_status=\'rejected\' AND u.role=\'teacher\'
             ORDER BY e.updated_at DESC'
        )->fetchAll();
    }

    public function updateApproval(int $id, string $status, ?int $adminId, ?string $reason): bool
    {
        $s = strtolower($status) === 'approved' ? 'approved' : 'rejected';
        $st = $this->db->prepare(
            'UPDATE evenements
             SET approval_status=?,approved_by=?,approved_at=NOW(),
                 rejection_reason=?,updated_at=CURRENT_TIMESTAMP
             WHERE id=?'
        );

        return $st->execute([$s, $adminId, $s === 'rejected' ? $reason : null, $id]);
    }

    public function findByCreator(int $userId): array
    {
        $st = $this->db->prepare(
            'SELECT e.*, COUNT(r.id) as resource_count
             FROM evenements e
             LEFT JOIN evenement_ressources r ON r.evenement_id = e.id
             WHERE e.created_by = ?
             GROUP BY e.id
             ORDER BY COALESCE(CONCAT(e.date_debut,\' \',e.heure_debut), e.event_date) ASC'
        );
        $st->execute([$userId]);

        return $st->fetchAll();
    }

    public function findByIdAndCreator(int $id, int $userId): array|false
    {
        $st = $this->db->prepare(
            'SELECT * FROM evenements WHERE id = ? AND created_by = ? LIMIT 1'
        );
        $st->execute([$id, $userId]);

        return $st->fetch();
    }

    public function markNonPendingAsPending(int $id): void
    {
        $st = $this->db->prepare(
            'UPDATE evenements SET approval_status=\'pending\', updated_at=CURRENT_TIMESTAMP
             WHERE id=? AND approval_status != \'pending\''
        );
        $st->execute([$id]);
    }

    public function findApprovedWithCreators(): array
    {
        return $this->db->query(
            'SELECT e.*, u.name as creator_name
             FROM evenements e
             JOIN users u ON e.created_by = u.id
             WHERE e.approval_status = \'approved\'
             ORDER BY COALESCE(CONCAT(e.date_debut,\' \',e.heure_debut), e.event_date) ASC'
        )->fetchAll();
    }

    public function findWithCreatorById(int $id): array|false
    {
        $st = $this->db->prepare(
            'SELECT e.*, u.name as creator_name, u.role as creator_role
             FROM evenements e
             JOIN users u ON u.id = e.created_by
             WHERE e.id = ? LIMIT 1'
        );
        $st->execute([$id]);

        return $st->fetch();
    }

    public function findApprovedById(int $id): array|false
    {
        $st = $this->db->prepare(
            'SELECT * FROM evenements WHERE id = ? AND approval_status = \'approved\' LIMIT 1'
        );
        $st->execute([$id]);

        return $st->fetch();
    }

    public function isCreatedBy(int $eventId, int $userId): bool
    {
        $st = $this->db->prepare(
            'SELECT id FROM evenements WHERE id = ? AND created_by = ? LIMIT 1'
        );
        $st->execute([$eventId, $userId]);

        return (bool) $st->fetch();
    }
}
