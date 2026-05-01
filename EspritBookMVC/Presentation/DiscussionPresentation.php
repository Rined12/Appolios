<?php

declare(strict_types=1);

/**
 * Presentation DTOs for discussion views (URLs and card shapes). Used by controllers only.
 */
final class DiscussionPresentation
{
    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    public static function studentIndexCards(array $rows, int $currentUserId, string $foPrefix, string $appEntry): array
    {
        $cards = [];
        foreach ($rows as $d) {
            $authorId = (int) ($d['id_auteur'] ?? $d['created_by'] ?? 0);
            $isAuthor = $authorId === $currentUserId;
            $id = (int) ($d['id_discussion'] ?? 0);
            $base = $appEntry . '?url=' . rawurlencode($foPrefix . '/discussions/' . $id);
            $cards[] = [
                'title' => (string) ($d['titre'] ?? 'Discussion'),
                'content' => (string) ($d['contenu'] ?? ''),
                'group_name' => (string) ($d['nom_groupe'] ?? 'N/A'),
                'is_author' => $isAuthor,
                'url_chat' => $base . '/chat',
                'url_edit' => $base . '/edit',
                'url_delete' => $base . '/delete',
            ];
        }

        return $cards;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    public static function groupShowCards(array $rows, int $viewerId, string $foPrefix, int $groupId, bool $isGroupCreatorViewer, string $appEntry): array
    {
        $cards = [];
        foreach ($rows as $d) {
            $discId = (int) ($d['id_discussion'] ?? $d['id'] ?? 0);
            $discAuthorId = (int) ($d['id_auteur'] ?? $d['created_by'] ?? 0);
            $canDelDisc = $discId > 0 && ($discAuthorId === $viewerId || $isGroupCreatorViewer);
            $cards[] = [
                'title' => (string) ($d['titre'] ?? 'Discussion'),
                'excerpt' => substr((string) ($d['contenu'] ?? ''), 0, 180),
                'author_name' => (string) ($d['auteur_name'] ?? 'Unknown'),
                'can_delete' => $canDelDisc,
                'can_chat' => $discId > 0,
                'disc_id' => $discId,
                'url_chat' => $appEntry . '?url=' . rawurlencode($foPrefix . '/discussions/' . $discId . '/chat'),
                'url_delete' => $appEntry . '?url=' . rawurlencode($foPrefix . '/groupes/' . $groupId . '/discussions/' . $discId . '/delete'),
            ];
        }

        return $cards;
    }

    /**
     * @param array<string, mixed> $discussionRow
     * @return array<string, mixed>
     */
    public static function editForm(array $discussionRow, string $foPrefix, string $appEntry): array
    {
        $id = (int) ($discussionRow['id_discussion'] ?? 0);

        return [
            'discussion_id' => $id,
            'update_url' => $appEntry . '?url=' . rawurlencode($foPrefix . '/discussions/' . $id . '/update'),
            'selected_group_id' => (int) ($discussionRow['id_groupe'] ?? $discussionRow['group_id'] ?? 0),
            'title_value' => (string) ($discussionRow['titre'] ?? ''),
            'content_value' => (string) ($discussionRow['contenu'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed> $discussionRow
     * @return array<string, string>
     */
    public static function chatUrls(array $discussionRow, string $foPrefix, string $appEntry): array
    {
        $id = (int) ($discussionRow['id_discussion'] ?? $discussionRow['id'] ?? 0);

        return [
            'back_url' => $appEntry . '?url=' . rawurlencode($foPrefix . '/discussions'),
            'upload_url' => $appEntry . '?url=' . rawurlencode($foPrefix . '/discussions/' . $id . '/upload'),
        ];
    }
}
