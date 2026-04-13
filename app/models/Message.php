<?php
/**
 * APPOLIOS — Social Learning
 * Model : Message
 */

require_once __DIR__ . '/../core/Model.php';

class Message extends Model {
    protected $table = 'message';

    // ------------------------------------------------------------------
    // READ
    // ------------------------------------------------------------------

    /**
     * Messages for a discussion — paginated — with author name.
     */
    /** Tous les messages d’une discussion (ordre chronologique), sans limite pratique. */
    public function listByDiscussion(int $idDiscussion): array {
        return $this->getByDiscussion($idDiscussion, 100000, 0);
    }

    public function getByDiscussion(int $idDiscussion, int $limit = 20, int $offset = 0): array {
        $sql = "SELECT m.*, u.name AS nom_auteur
                FROM message m
                JOIN users u ON u.id = m.id_auteur
                WHERE m.id_discussion = ?
                ORDER BY m.date_envoi ASC
                LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $idDiscussion, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countByDiscussion(int $idDiscussion): int {
        $sql = "SELECT COUNT(*) FROM message WHERE id_discussion = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idDiscussion]);
        return (int) $stmt->fetchColumn();
    }

    public function findByIdWithAuthor(int $id): ?array {
        $sql = "SELECT m.*, u.name AS nom_auteur
                FROM message m
                JOIN users u ON u.id = m.id_auteur
                WHERE m.id_message = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Ligne message brute (sans jointure). */
    public function findById(int|string $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM message WHERE id_message = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Total message count across the whole platform (for admin stats). */
    public function countAll(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM message");
        return (int) $stmt->fetchColumn();
    }

    // ------------------------------------------------------------------
    // CREATE / DELETE
    // ------------------------------------------------------------------

    public function create(string $contenu, int $idDiscussion, int $idAuteur): int|false {
        $sql = "INSERT INTO message (contenu, date_envoi, id_discussion, id_auteur)
                VALUES (?, NOW(), ?, ?)";
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute([$contenu, $idDiscussion, $idAuteur])) {
            return (int) $this->db->lastInsertId();
        }
        return false;
    }

    public function deleteById(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM message WHERE id_message = ?");
        return $stmt->execute([$id]);
    }
}
