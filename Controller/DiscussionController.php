<?php

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../config/database.php';

class DiscussionController extends BaseController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getConnection();
    }

    public function fetchAllWithGroupAndAuthor(?string $approval = null): array
    {
        $sql =
            'SELECT d.*, g.nom_groupe, u.name AS auteur_name
             FROM discussion d
             LEFT JOIN groupe g ON g.id_groupe = d.id_groupe
             LEFT JOIN users u ON u.id = d.id_auteur';

        $params = [];
        if ($approval !== null && in_array($approval, ['en_cours', 'approuve', 'rejete'], true)) {
            $sql .= ' WHERE d.approval_statut = ?';
            $params[] = $approval;
        }

        $sql .= ' ORDER BY d.date_creation DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function fetchRowByPk(int $discussionId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM discussion WHERE id_discussion = ? LIMIT 1');
        $stmt->execute([$discussionId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function fetchVisibleForUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT DISTINCT d.*, g.nom_groupe
             FROM discussion d
             LEFT JOIN groupe_user gu ON gu.id_groupe = d.id_groupe
             LEFT JOIN groupe g ON g.id_groupe = d.id_groupe
             WHERE gu.id_user = ? OR d.id_auteur = ?
             ORDER BY d.date_creation DESC'
        );
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll();
    }

    public function fetchByGroup(int $groupId): array
    {
        $stmt = $this->db->prepare(
            'SELECT d.*, u.name AS auteur_name
             FROM discussion d
             LEFT JOIN users u ON u.id = d.id_auteur
             WHERE d.id_groupe = ?
             ORDER BY d.date_creation DESC'
        );
        $stmt->execute([$groupId]);
        return $stmt->fetchAll();
    }

    public function fetchByAuthor(int $authorId): array
    {
        $stmt = $this->db->prepare(
            'SELECT d.*, g.nom_groupe
             FROM discussion d
             LEFT JOIN groupe g ON g.id_groupe = d.id_groupe
             WHERE d.id_auteur = ?
             ORDER BY d.date_creation DESC'
        );
        $stmt->execute([$authorId]);
        return $stmt->fetchAll();
    }

    public function findOwnedBy(int $discussionId, int $authorId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT d.*, d.id_discussion AS id_discussion, d.titre AS titre, d.contenu AS contenu, d.id_groupe AS id_groupe, d.date_creation AS date_creation
             FROM discussion d
             WHERE d.id_discussion = ? AND d.id_auteur = ?
             LIMIT 1'
        );
        $stmt->execute([$discussionId, $authorId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function createForGroup(int $groupId, int $authorId, string $title, string $content, ?string $initialApproval = null): bool
    {
        $approval = $initialApproval;
        if ($approval === null || !in_array($approval, ['en_cours', 'approuve', 'rejete'], true)) {
            $approval = 'approuve';
        }

        $stmt = $this->db->prepare(
            'INSERT INTO discussion (titre, contenu, date_creation, id_groupe, id_auteur, approval_statut)
             VALUES (?, ?, NOW(), ?, ?, ?)'
        );
        return $stmt->execute([$title, $content, $groupId, $authorId, $approval]);
    }

    public function updateOwned(int $discussionId, int $authorId, string $title, string $content, int $groupId): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE discussion
             SET titre = ?, contenu = ?, id_groupe = ?
             WHERE id_discussion = ? AND id_auteur = ?'
        );
        return $stmt->execute([$title, $content, $groupId, $discussionId, $authorId]);
    }

    public function deleteByPrimaryKey(int $discussionId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM discussion WHERE id_discussion = ?');
        return $stmt->execute([$discussionId]);
    }

    public function deleteAllForGroup(int $groupId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM discussion WHERE id_groupe = ?');
        return $stmt->execute([$groupId]);
    }

    private function authorizeDiscussion(int $discussionId): void
    {
        if (!$this->isLoggedIn() || !in_array($_SESSION['role'] ?? '', ['student', 'teacher'], true)) {
            $this->jsonResponse(['ok' => false, 'error' => 'Unauthorized.'], 401);
        }

        if ($discussionId <= 0) {
            $this->jsonResponse(['ok' => false, 'error' => 'Invalid discussion id.'], 422);
        }

        $userId = (int) ($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->jsonResponse(['ok' => false, 'error' => 'Unauthorized.'], 401);
        }

        $groupeModel = $this->model('Groupe');

        $discussion = $this->fetchRowByPk($discussionId);
        if (!$discussion) {
            $this->jsonResponse(['ok' => false, 'error' => 'Discussion not found.'], 404);
        }

        $groupId = (int) ($discussion['id_groupe'] ?? 0);
        if ($groupId <= 0) {
            $this->jsonResponse(['ok' => false, 'error' => 'Parent group not found.'], 404);
        }

        $group = $groupeModel->findById($groupId);
        if (!$group) {
            $this->jsonResponse(['ok' => false, 'error' => 'Parent group not found.'], 404);
        }

        $isOwner = (int) ($group['id_createur'] ?? 0) === $userId;
        $isMember = $groupeModel->estMembre($groupId, $userId);
        $discAuthorId = (int) ($discussion['id_auteur'] ?? 0);
        $isDiscussionAuthor = $discAuthorId === $userId && $discAuthorId > 0;

        if (!$isOwner && !$isMember && !$isDiscussionAuthor) {
            $this->jsonResponse(['ok' => false, 'error' => 'You must join the group to use live locations.'], 403);
        }
    }

    public function shareLocation(): void
    {
        $raw = file_get_contents('php://input');
        $body = json_decode((string) $raw, true);

        $discussionId = (int) ($body['discussion_id'] ?? 0);
        $lat = (float) ($body['latitude'] ?? 0);
        $lng = (float) ($body['longitude'] ?? 0);
        $durationMinutes = (int) ($body['duration'] ?? 0);

        if ($discussionId <= 0) {
            $this->jsonResponse(['ok' => false, 'error' => 'discussion_id is required.'], 422);
        }
        if ($lat === 0.0 && $lng === 0.0) {
            $this->jsonResponse(['ok' => false, 'error' => 'latitude/longitude are required.'], 422);
        }
        if ($durationMinutes <= 0) {
            $this->jsonResponse(['ok' => false, 'error' => 'duration must be positive.'], 422);
        }

        $this->authorizeDiscussion($discussionId);

        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $expiresAt = date('Y-m-d H:i:s', time() + ($durationMinutes * 60));

        $pdo = getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO live_locations
                (user_id, discussion_id, latitude, longitude, expires_at, is_active)
            VALUES (?, ?, ?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE
                latitude = VALUES(latitude),
                longitude = VALUES(longitude),
                expires_at = VALUES(expires_at),
                shared_at = NOW(),
                is_active = 1"
        );
        $stmt->execute([$userId, $discussionId, $lat, $lng, $expiresAt]);

        $this->jsonResponse(['success' => true]);
    }

    public function getLiveLocations(): void
    {
        $discussionId = (int) ($_GET['discussion_id'] ?? 0);
        if ($discussionId <= 0) {
            $this->jsonResponse([], 200);
        }

        $this->authorizeDiscussion($discussionId);

        $pdo = getConnection();
        $stmt = $pdo->prepare(
            "SELECT
                ll.user_id,
                u.name AS user_name,
                ll.latitude,
                ll.longitude,
                ll.shared_at,
                ll.expires_at
            FROM live_locations ll
            JOIN users u ON u.id = ll.user_id
            WHERE ll.discussion_id = ?
              AND ll.is_active = 1
              AND ll.expires_at > NOW()"
        );
        $stmt->execute([$discussionId]);
        $rows = $stmt->fetchAll();

        $this->jsonResponse($rows);
    }

    public function stopLocation(): void
    {
        $raw = file_get_contents('php://input');
        $body = json_decode((string) $raw, true);

        $discussionId = (int) ($body['discussion_id'] ?? 0);
        if ($discussionId <= 0) {
            $this->jsonResponse(['ok' => false, 'error' => 'discussion_id is required.'], 422);
        }

        $this->authorizeDiscussion($discussionId);

        $userId = (int) ($_SESSION['user_id'] ?? 0);

        $pdo = getConnection();
        $stmt = $pdo->prepare(
            'UPDATE live_locations
             SET is_active = 0
             WHERE user_id = ? AND discussion_id = ?'
        );
        $stmt->execute([$userId, $discussionId]);

        $this->jsonResponse(['success' => true]);
    }

    public function discussionMessagesTableExists(): bool
    {
        try {
            $stmt = $this->db->query("SHOW TABLES LIKE 'discussion_messages'");

            return (bool) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** Persisted Socket.IO chat rows tied to discussions in this group. */
    public function countChatMessagesForGroup(int $groupId): int
    {
        if ($groupId <= 0 || !$this->discussionMessagesTableExists()) {
            return 0;
        }
        $stmt = $this->db->prepare(
            'SELECT COUNT(*)
             FROM discussion_messages dm
             INNER JOIN discussion d ON d.id_discussion = dm.discussion_id
             WHERE d.id_groupe = ?'
        );
        $stmt->execute([$groupId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * @return list<array{id_discussion:int|string,titre:string,date_creation:?string,chat_messages:int|string}>
     */
    public function fetchTopDiscussionsByChatVolume(int $groupId, int $limit = 8): array
    {
        if ($groupId <= 0 || !$this->discussionMessagesTableExists()) {
            return [];
        }
        $limit = max(1, min(50, $limit));
        $stmt = $this->db->prepare(
            "SELECT d.id_discussion, d.titre, d.date_creation, COUNT(dm.id) AS chat_messages
             FROM discussion d
             LEFT JOIN discussion_messages dm ON dm.discussion_id = d.id_discussion
             WHERE d.id_groupe = ?
             GROUP BY d.id_discussion, d.titre, d.date_creation
             ORDER BY chat_messages DESC, d.date_creation DESC
             LIMIT {$limit}"
        );
        $stmt->execute([$groupId]);

        return $stmt->fetchAll();
    }

    /**
     * @return list<array{user_name:?string,message:?string,message_type:?string,file_name:?string,created_at:?string,discussion_title:?string}>
     */
    public function fetchRecentChatForGroup(int $groupId, int $limit = 10): array
    {
        if ($groupId <= 0 || !$this->discussionMessagesTableExists()) {
            return [];
        }
        $limit = max(1, min(50, $limit));
        $stmt = $this->db->prepare(
            "SELECT dm.user_name, dm.message, dm.message_type, dm.file_name, dm.created_at, d.titre AS discussion_title
             FROM discussion_messages dm
             INNER JOIN discussion d ON d.id_discussion = dm.discussion_id
             WHERE d.id_groupe = ?
             ORDER BY dm.created_at DESC
             LIMIT {$limit}"
        );
        $stmt->execute([$groupId]);

        return $stmt->fetchAll();
    }
}
