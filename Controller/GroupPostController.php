<?php

require_once __DIR__ . '/../config/database.php';

class GroupPostController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getConnection();
    }

    public function create(array $data)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO group_posts (group_id, user_id, post_type, content, media_url, media_kind, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())'
        );

        $ok = $stmt->execute([
            (int) ($data['group_id'] ?? 0),
            (int) ($data['user_id'] ?? 0),
            (string) ($data['post_type'] ?? 'text'),
            ($data['content'] ?? null) !== '' ? (string) $data['content'] : null,
            ($data['media_url'] ?? null) !== '' ? (string) $data['media_url'] : null,
            ($data['media_kind'] ?? null) !== '' ? (string) $data['media_kind'] : null,
        ]);

        if (!$ok) {
            return false;
        }

        return (int) $this->db->lastInsertId();
    }

    public function fetchByGroup(int $groupId, int $limit = 50): array
    {
        $limit = max(1, min(200, $limit));
        $stmt = $this->db->prepare(
            'SELECT p.*, u.name AS user_name
             FROM group_posts p
             JOIN users u ON u.id = p.user_id
             WHERE p.group_id = ?
             ORDER BY p.created_at DESC
             LIMIT ' . $limit
        );
        $stmt->execute([$groupId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM group_posts WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function deletePost(int $postId, int $requestUserId, bool $allowAdminOverride = false): bool
    {
        $post = $this->findById($postId);
        if (!$post) {
            return false;
        }

        $isOwner = ((int) ($post['user_id'] ?? 0)) === $requestUserId;
        if (!$isOwner && !$allowAdminOverride) {
            return false;
        }

        $stmt = $this->db->prepare('DELETE FROM group_posts WHERE id = ?');
        return $stmt->execute([$postId]);
    }
}
