<?php

require_once __DIR__ . '/../config/database.php';

class GroupPostCommentController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getConnection();
    }

    public function add(int $postId, int $userId, string $content)
    {
        $content = trim($content);
        if ($content === '') {
            return false;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO group_post_comments (post_id, user_id, content, created_at)
             VALUES (?, ?, ?, NOW())'
        );
        $ok = $stmt->execute([$postId, $userId, $content]);
        if (!$ok) {
            return false;
        }
        return (int) $this->db->lastInsertId();
    }

    public function fetchByPost(int $postId, int $limit = 50): array
    {
        $limit = max(1, min(200, $limit));
        $stmt = $this->db->prepare(
            'SELECT c.*, u.name AS user_name
             FROM group_post_comments c
             JOIN users u ON u.id = c.user_id
             WHERE c.post_id = ?
             ORDER BY c.created_at ASC
             LIMIT ' . $limit
        );
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }

    public function deleteComment(int $commentId, int $requestUserId, bool $allowAdminOverride = false): bool
    {
        $stmt = $this->db->prepare('SELECT * FROM group_post_comments WHERE id = ? LIMIT 1');
        $stmt->execute([$commentId]);
        $row = $stmt->fetch();
        if (!$row) {
            return false;
        }

        $isOwner = ((int) ($row['user_id'] ?? 0)) === $requestUserId;
        if (!$isOwner && !$allowAdminOverride) {
            return false;
        }

        $del = $this->db->prepare('DELETE FROM group_post_comments WHERE id = ?');
        return $del->execute([$commentId]);
    }
}
