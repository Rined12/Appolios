<?php

require_once __DIR__ . '/../config/database.php';

class GroupPostReactionController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getConnection();
    }

    public function setReaction(int $postId, int $userId, string $reaction): bool
    {
        $reaction = trim($reaction);
        if ($reaction === '') {
            return false;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO group_post_reactions (post_id, user_id, reaction, created_at)
             VALUES (?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE reaction = VALUES(reaction), created_at = NOW()'
        );

        return $stmt->execute([$postId, $userId, $reaction]);
    }

    public function removeReaction(int $postId, int $userId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM group_post_reactions WHERE post_id = ? AND user_id = ?');
        return $stmt->execute([$postId, $userId]);
    }

    public function countByPost(int $postId): array
    {
        $stmt = $this->db->prepare(
            'SELECT reaction, COUNT(*) AS cnt
             FROM group_post_reactions
             WHERE post_id = ?
             GROUP BY reaction'
        );
        $stmt->execute([$postId]);
        $rows = $stmt->fetchAll();

        $out = [];
        foreach ($rows as $row) {
            $key = (string) ($row['reaction'] ?? '');
            if ($key === '') {
                continue;
            }
            $out[$key] = (int) ($row['cnt'] ?? 0);
        }
        return $out;
    }

    public function getUserReaction(int $postId, int $userId): ?string
    {
        $stmt = $this->db->prepare('SELECT reaction FROM group_post_reactions WHERE post_id = ? AND user_id = ? LIMIT 1');
        $stmt->execute([$postId, $userId]);
        $val = $stmt->fetchColumn();
        if ($val === false || $val === null) {
            return null;
        }
        return (string) $val;
    }
}
