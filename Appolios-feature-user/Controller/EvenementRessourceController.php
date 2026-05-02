<?php
/**
 * APPOLIOS Evenement Resource Controller
 * Handles evenement resource-related business logic
 */

require_once __DIR__ . '/../Model/BaseModel.php';

class EvenementRessourceController extends BaseModel {
    protected string $table = 'evenement_ressources';
    
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (evenement_id, type, title, details, created_by, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['evenement_id'],
                $data['type'],
                $data['title'],
                $data['details'],
                $data['created_by']
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function existsInListScope($id, $evenementId, $type) {
        $sql = "SELECT id FROM {$this->table} WHERE id = ? AND evenement_id = ? AND type = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $id, (int) $evenementId, $type]);
        return (bool) $stmt->fetch();
    }

    public function getByType($type) {
        $sql = "SELECT r.*, u.name as creator_name, e.title as evenement_title
                FROM {$this->table} r
                JOIN users u ON r.created_by = u.id
                JOIN evenements e ON r.evenement_id = e.id
                WHERE r.type = ?
                ORDER BY r.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }

    public function getByTypeAndEvenement($type, $evenementId) {
        $sql = "SELECT r.*, u.name as creator_name, e.title as evenement_title
                FROM {$this->table} r
                JOIN users u ON r.created_by = u.id
                JOIN evenements e ON r.evenement_id = e.id
                WHERE r.type = ? AND r.evenement_id = ?
                ORDER BY r.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$type, $evenementId]);
        return $stmt->fetchAll();
    }

    public function getGroupedByEvenement($evenementId) {
        return [
            'rules' => $this->getByTypeAndEvenement('rule', $evenementId),
            'materiels' => $this->getByTypeAndEvenement('materiel', $evenementId),
            'plans' => $this->getByTypeAndEvenement('plan', $evenementId)
        ];
    }

    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table}
                SET title = ?, details = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND evenement_id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['details'],
            $id,
            $data['evenement_id']
        ]);
    }

    public function delete($id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function deleteByEvenement($id, $evenementId) {
        $sql = "DELETE FROM {$this->table} WHERE id = ? AND evenement_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id, $evenementId]);
    }
}