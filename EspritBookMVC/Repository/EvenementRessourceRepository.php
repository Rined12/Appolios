<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseRepository.php';

/**
 * Persistence for evenement_ressources (rules, materiel, plans, participation rows).
 */
class EvenementRessourceRepository extends BaseRepository
{
    protected string $table = 'evenement_ressources';

    private static ?bool $tableExistsCache = null;

    public function ressourcesTableExists(): bool
    {
        if (self::$tableExistsCache !== null) {
            return self::$tableExistsCache;
        }
        try {
            $st = $this->db->query("SHOW TABLES LIKE 'evenement_ressources'");
            self::$tableExistsCache = $st->fetch() !== false;
        } catch (PDOException $e) {
            self::$tableExistsCache = false;
        }

        return self::$tableExistsCache;
    }

    public function findById(int $id): array|false
    {
        $st = $this->db->prepare('SELECT * FROM evenement_ressources WHERE id = ? LIMIT 1');
        $st->execute([$id]);

        return $st->fetch();
    }

    /**
     * Admin / teacher list: includes evenement title.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findByTypeAndEvent(string $type, int $evenementId): array
    {
        $st = $this->db->prepare(
            'SELECT r.*, u.name as creator_name, e.title as evenement_title
             FROM evenement_ressources r
             JOIN users u ON r.created_by = u.id
             JOIN evenements e ON r.evenement_id = e.id
             WHERE r.type = ? AND r.evenement_id = ?
             ORDER BY r.created_at DESC'
        );
        $st->execute([$type, $evenementId]);

        return $st->fetchAll();
    }

    /**
     * Student view: no join on event title column.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findByTypeAndEventForStudent(string $type, int $evenementId): array
    {
        if (!$this->ressourcesTableExists()) {
            return [];
        }
        $st = $this->db->prepare(
            'SELECT r.*, u.name as creator_name
             FROM evenement_ressources r
             JOIN users u ON r.created_by = u.id
             WHERE r.type = ? AND r.evenement_id = ?
             ORDER BY r.created_at DESC'
        );
        $st->execute([$type, $evenementId]);

        return $st->fetchAll();
    }

    public function existsInScope(int $id, int $evenementId, string $type): bool
    {
        $st = $this->db->prepare(
            'SELECT id FROM evenement_ressources WHERE id=? AND evenement_id=? AND type=? LIMIT 1'
        );
        $st->execute([$id, $evenementId, $type]);

        return (bool) $st->fetch();
    }

    /**
     * @param array{evenement_id:int,type:string,title:string,details:string,created_by:int} $d
     */
    public function create(array $d): int|false
    {
        try {
            $st = $this->db->prepare(
                'INSERT INTO evenement_ressources (evenement_id,type,title,details,created_by,created_at)
                 VALUES (?,?,?,?,?,NOW())'
            );
            $st->execute([
                $d['evenement_id'],
                $d['type'],
                $d['title'],
                $d['details'],
                $d['created_by'],
            ]);

            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * @param array{title:string,details:string,evenement_id:int} $d
     */
    public function update(int $id, array $d): bool
    {
        $st = $this->db->prepare(
            'UPDATE evenement_ressources SET title=?,details=?,updated_at=CURRENT_TIMESTAMP
             WHERE id=? AND evenement_id=?'
        );

        return $st->execute([$d['title'], $d['details'], $id, $d['evenement_id']]);
    }

    public function delete(int $id, int $evenementId): bool
    {
        $st = $this->db->prepare(
            'DELETE FROM evenement_ressources WHERE id=? AND evenement_id=?'
        );

        return $st->execute([$id, $evenementId]);
    }

    /**
     * Group rule / materiel / plan rows by type for approval UI.
     *
     * @return array{rule: array<int, array{title: string, details: string}>, materiel: array<int, array{title: string, details: string}>, plan: array<int, array{title: string, details: string}>}
     */
    public function getGroupedPublicRessources(int $evenementId): array
    {
        $st = $this->db->prepare(
            'SELECT type, title, details FROM evenement_ressources
             WHERE evenement_id = ? AND type IN (\'rule\',\'materiel\',\'plan\')
             ORDER BY type, created_at ASC'
        );
        $st->execute([$evenementId]);
        $rows = $st->fetchAll();
        $grouped = ['rule' => [], 'materiel' => [], 'plan' => []];
        foreach ($rows as $r) {
            $t = $r['type'];
            if (!isset($grouped[$t])) {
                continue;
            }
            $grouped[$t][] = ['title' => $r['title'], 'details' => $r['details'] ?? ''];
        }

        return $grouped;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findParticipationsByEvent(int $eventId): array
    {
        $st = $this->db->prepare(
            'SELECT r.id, r.evenement_id, r.created_by as student_id,
                    r.title as student_name, r.details as status, r.created_at,
                    (SELECT u.email FROM users u WHERE u.id = r.created_by LIMIT 1) as student_email
             FROM evenement_ressources r
             WHERE r.evenement_id = ? AND r.type = \'participation\'
             ORDER BY r.created_at DESC'
        );
        $st->execute([$eventId]);

        return $st->fetchAll();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findParticipationsByCreator(int $teacherId): array
    {
        $st = $this->db->prepare(
            'SELECT r.id, r.evenement_id, r.created_by as student_id,
                    r.title as student_name, r.details as status, r.created_at,
                    e.title as event_title, e.date_debut, e.heure_debut,
                    u.name as student_name_full, u.email as student_email
             FROM evenement_ressources r
             JOIN evenements e ON r.evenement_id = e.id
             JOIN users u ON r.created_by = u.id
             WHERE r.type = \'participation\' AND e.created_by = ?
             ORDER BY r.created_at DESC'
        );
        $st->execute([$teacherId]);

        return $st->fetchAll();
    }

    public function findParticipationById(int $id): array|false
    {
        $st = $this->db->prepare(
            'SELECT * FROM evenement_ressources WHERE id = ? AND type = \'participation\' LIMIT 1'
        );
        $st->execute([$id]);

        return $st->fetch();
    }

    /**
     * Admin UI: normalize status to approved/rejected/pending.
     */
    public function updateParticipationStatusAdmin(int $id, string $status, ?string $reason = null): bool
    {
        $s = in_array($status, ['approved', 'rejected'], true) ? $status : 'pending';
        $st = $this->db->prepare(
            'UPDATE evenement_ressources
             SET details = ?, rejection_reason = ?, updated_at = CURRENT_TIMESTAMP
             WHERE id = ? AND type = \'participation\''
        );

        return $st->execute([$s, $reason, $id]);
    }

    /**
     * Teacher UI: stores raw status in details.
     */
    public function updateParticipationStatusTeacher(int $id, string $status, ?string $reason = null): bool
    {
        $st = $this->db->prepare(
            'UPDATE evenement_ressources
             SET details = ?, rejection_reason = ?, updated_at = CURRENT_TIMESTAMP
             WHERE id = ? AND type = \'participation\''
        );

        return $st->execute([$status, $reason, $id]);
    }

    public function deleteParticipationForEvent(int $id, int $evenementId): int
    {
        $st = $this->db->prepare(
            'DELETE FROM evenement_ressources WHERE id = ? AND evenement_id = ? AND type = \'participation\''
        );
        $st->execute([(int) $id, $evenementId]);

        return $st->rowCount();
    }

    public function deleteParticipationById(int $id): int
    {
        $st = $this->db->prepare(
            'DELETE FROM evenement_ressources WHERE id = ? AND type = \'participation\''
        );
        $st->execute([(int) $id]);

        return $st->rowCount();
    }

    /**
     * @return array<int, int|string>
     */
    public function getParticipationMapForStudent(int $studentId): array
    {
        if (!$this->ressourcesTableExists()) {
            return [];
        }
        $st = $this->db->prepare(
            'SELECT evenement_id, details as status
             FROM evenement_ressources
             WHERE type = \'participation\' AND created_by = ?'
        );
        $st->execute([$studentId]);
        $map = [];
        foreach ($st->fetchAll() as $row) {
            $map[(int) $row['evenement_id']] = $row['status'];
        }

        return $map;
    }

    public function findStudentParticipation(int $eventId, int $studentId): array|false
    {
        if (!$this->ressourcesTableExists()) {
            return false;
        }
        $st = $this->db->prepare(
            'SELECT * FROM evenement_ressources
             WHERE evenement_id = ? AND created_by = ? AND type = \'participation\' LIMIT 1'
        );
        $st->execute([$eventId, $studentId]);

        return $st->fetch();
    }

    public function createPendingParticipation(int $eventId, int $studentId, string $studentName): bool
    {
        if (!$this->ressourcesTableExists()) {
            return false;
        }
        try {
            $st = $this->db->prepare(
                'INSERT INTO evenement_ressources (evenement_id, type, title, details, created_by, created_at)
                 VALUES (?, \'participation\', ?, \'pending\', ?, NOW())'
            );

            return $st->execute([$eventId, $studentName, $studentId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function cancelPendingParticipation(int $eventId, int $studentId): bool
    {
        if (!$this->ressourcesTableExists()) {
            return false;
        }
        $st = $this->db->prepare(
            'DELETE FROM evenement_ressources
             WHERE evenement_id = ? AND created_by = ? AND type = \'participation\' AND details = \'pending\''
        );

        return $st->execute([$eventId, $studentId]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findMyParticipations(int $studentId): array
    {
        if (!$this->ressourcesTableExists()) {
            return [];
        }
        $st = $this->db->prepare(
            'SELECT e.*, er.id as p_id, er.details as p_status, er.rejection_reason, er.created_at as p_date, er.updated_at as p_update_date, u.name as creator_name
             FROM evenements e
             JOIN evenement_ressources er ON e.id = er.evenement_id
             JOIN users u ON e.created_by = u.id
             WHERE er.type = \'participation\' AND er.created_by = ?
             ORDER BY er.created_at DESC'
        );
        $st->execute([$studentId]);

        return $st->fetchAll();
    }

    public function findApprovedTicketForStudent(int $participationId, int $studentId): array|false
    {
        if (!$this->ressourcesTableExists()) {
            return false;
        }
        $st = $this->db->prepare(
            'SELECT er.*, e.title as event_title, e.location as event_location,
                    COALESCE(CONCAT(e.date_debut, \' \', e.heure_debut), e.event_date) as event_full_date,
                    u.name as student_name, u.email as student_email
             FROM evenement_ressources er
             JOIN evenements e ON er.evenement_id = e.id
             JOIN users u ON er.created_by = u.id
             WHERE er.id = ? AND er.created_by = ? AND er.type = \'participation\' AND er.details = \'approved\'
             LIMIT 1'
        );
        $st->execute([$participationId, $studentId]);

        return $st->fetch();
    }
}
