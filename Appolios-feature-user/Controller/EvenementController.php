<?php
/**
 * APPOLIOS Evenement Controller
 * Handles evenement-related business logic
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class EvenementController extends BaseModel {
    protected string $table = 'evenements';
    
    public function create($data) {
        $sql = "INSERT INTO {$this->table}
            (title, titre, description, date_debut, date_fin, heure_debut, heure_fin, lieu, capacite_max, type, statut, approval_status, location, event_date, created_by, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

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
}