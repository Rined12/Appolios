<?php
declare(strict_types=1);

/**
 * View-layer presenters and display formatters (no DB/session; GroupPresenter receives repository from controller).
 */
require_once __DIR__ . '/../Model/SessionEntities.php';
require_once __DIR__ . '/../Model/Repositories.php';

/**
 * Date/text formatting for views (called from controllers only).
 */
final class DisplayFormatter
{
    public static function longMonthDate(?string $mysqlDatetime): string
    {
        if ($mysqlDatetime === null || $mysqlDatetime === '') {
            return '';
        }
        $ts = strtotime($mysqlDatetime);

        return $ts !== false ? date('F d, Y', $ts) : '';
    }

    public static function mediumDateTime(?string $mysqlDatetime): string
    {
        if ($mysqlDatetime === null || $mysqlDatetime === '') {
            return '';
        }
        $ts = strtotime($mysqlDatetime);

        return $ts !== false ? date('M d, Y H:i', $ts) : '';
    }

    public static function shortMonthDate(?string $mysqlDatetime): string
    {
        if ($mysqlDatetime === null || $mysqlDatetime === '') {
            return '';
        }
        $ts = strtotime($mysqlDatetime);

        return $ts !== false ? date('M d, Y', $ts) : '';
    }

    public static function timeHm(?string $mysqlDatetime): string
    {
        if ($mysqlDatetime === null || $mysqlDatetime === '') {
            return '';
        }
        $ts = strtotime($mysqlDatetime);

        return $ts !== false ? date('H:i', $ts) : '';
    }

    public static function rejectionModalApprovedLabel(?string $mysqlDatetime): string
    {
        if ($mysqlDatetime === null || $mysqlDatetime === '') {
            return 'Unknown date';
        }
        $ts = strtotime($mysqlDatetime);

        return $ts !== false ? date('d M Y \a\t H:i', $ts) : 'Unknown date';
    }

    public static function humanParticipationUpdated(?string $mysqlDatetime): string
    {
        if ($mysqlDatetime === null || $mysqlDatetime === '') {
            return 'Recently';
        }
        $ts = strtotime($mysqlDatetime);

        return $ts !== false ? date('d M Y at H:i', $ts) : 'Recently';
    }

    public static function shortDayMonth(?string $mysqlDatetime): string
    {
        if ($mysqlDatetime === null || $mysqlDatetime === '') {
            return '';
        }
        $ts = strtotime($mysqlDatetime);

        return $ts !== false ? date('d M', $ts) : '';
    }

    public static function formMinDateTomorrow(): string
    {
        return date('Y-m-d', strtotime('+1 day'));
    }
}

/**
 * Discussion UI DTOs: cards, URLs, composer republish shaping, flat error lines.
 * Does not read $_SESSION / $_POST (those stay in the controller).
 */
final class DiscussionPresenter
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
     * Search autocomplete suggestions derived from discussion index cards (controller builds cards once).
     *
     * @param array<int, array<string, mixed>> $discussionCards
     * @return array<int, string>
     */
    public static function discussionSearchSuggestionsFromCards(array $discussionCards): array
    {
        $uniq = [];
        foreach ($discussionCards as $card) {
            $title = trim((string) ($card['title'] ?? ''));
            $group = trim((string) ($card['group_name'] ?? ''));
            if ($title !== '') {
                $uniq[$title] = true;
            }
            if ($group !== '') {
                $uniq[$group] = true;
            }
        }
        $keys = array_values(array_filter(array_keys($uniq), static function (string $v): bool {
            return mb_strlen(trim($v)) >= 2;
        }));

        return array_slice($keys, 0, 80);
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

    /**
     * @param array<string, mixed> $fieldErrors
     * @return array{old: array<string, mixed>, error_messages: array<int, string>}
     */
    public static function groupDetailComposerPayload(array $old, array $fieldErrors): array
    {
        return [
            'old' => $old,
            'error_messages' => self::flattenFieldErrors($fieldErrors),
        ];
    }

    /**
     * @param array<string, mixed> $errorsByField
     * @return array<int, string>
     */
    public static function flattenFieldErrors(array $errorsByField): array
    {
        $messages = [];
        foreach ($errorsByField as $msg) {
            $s = trim((string) $msg);
            if ($s !== '') {
                $messages[] = $s;
            }
        }

        return $messages;
    }
}

/**
 * Normalizes flash messages for dumb templates (no $_SESSION in views).
 *
 * @return array{message: string, alert_class: string, inner_style: string}|null
 */
final class FlashBannerPresenter
{
    public static function fromFlash(?FlashMessageEntity $flash): ?array
    {
        if ($flash === null || $flash->getMessage() === '') {
            return null;
        }
        $isError = $flash->getType() === 'error';

        return [
            'message' => $flash->getMessage(),
            'alert_class' => $isError ? 'danger' : 'success',
            'inner_style' => $isError
                ? 'background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.3); color: #dc3545;'
                : 'background: rgba(25, 135, 84, 0.1); border: 1px solid rgba(25, 135, 84, 0.3); color: #198754;',
        ];
    }
}

/**
 * Normalizes course video URLs for dumb templates (no parsing logic in views).
 *
 * @return array{type: 'youtube', embed_url: string}|array{type: 'mp4', src: string}|array{type: 'none'}
 */
final class CourseVideoPresenter
{
    public static function normalizeVideo(string $videoUrl): array
    {
        $videoUrl = trim($videoUrl);
        if ($videoUrl === '') {
            return ['type' => 'none'];
        }

        if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
            if (strpos($videoUrl, 'youtu.be') !== false) {
                $videoId = basename((string) parse_url($videoUrl, PHP_URL_PATH));
            } else {
                parse_str((string) parse_url($videoUrl, PHP_URL_QUERY), $params);
                $videoId = (string) ($params['v'] ?? '');
            }
            $videoId = preg_replace('/[^a-zA-Z0-9_-]/', '', $videoId) ?? '';
            if ($videoId === '') {
                return ['type' => 'none'];
            }

            return [
                'type' => 'youtube',
                'embed_url' => 'https://www.youtube.com/embed/' . $videoId,
            ];
        }

        return ['type' => 'mp4', 'src' => $videoUrl];
    }
}

/**
 * Participation summary counts for resource/event views.
 *
 * @param array<int, array<string, mixed>> $participations
 * @return array{total: int, pending_count: int, approved_count: int, rejected_count: int}
 */
final class ParticipationRollupPresenter
{
    public static function rollup(array $participations): array
    {
        $pending = 0;
        $approved = 0;
        $rejected = 0;
        foreach ($participations as $p) {
            $s = (string) ($p['status'] ?? '');
            if ($s === 'pending') {
                ++$pending;
            } elseif ($s === 'approved') {
                ++$approved;
            } elseif ($s === 'rejected') {
                ++$rejected;
            }
        }

        return [
            'total' => count($participations),
            'pending_count' => $pending,
            'approved_count' => $approved,
            'rejected_count' => $rejected,
        ];
    }
}

/**
 * Buckets participation rows by status for teacher request screens.
 *
 * @param array<int, array<string, mixed>> $requests
 * @return array{pending: array, approved: array, rejected: array, counts: array{pending:int,approved:int,rejected:int}}
 */
final class ParticipationBucketsPresenter
{
    public static function bucket(array $requests): array
    {
        $pending = [];
        $approved = [];
        $rejected = [];
        foreach ($requests as $r) {
            $s = (string) ($r['status'] ?? '');
            if ($s === 'pending') {
                $pending[] = $r;
            } elseif ($s === 'approved') {
                $approved[] = $r;
            } elseif ($s === 'rejected') {
                $rejected[] = $r;
            }
        }

        return [
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'counts' => [
                'pending' => count($pending),
                'approved' => count($approved),
                'rejected' => count($rejected),
            ],
        ];
    }

    /**
     * Adds display_updated_short per row for templates.
     *
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    public static function withProcessedDates(array $rows): array
    {
        return array_map(static function (array $req): array {
            $label = DisplayFormatter::shortDayMonth((string) ($req['updated_at'] ?? ''));

            return array_merge($req, [
                'display_processed_short' => $label !== '' ? $label : 'Recently',
            ]);
        }, $rows);
    }
}

/**
 * Display fields for a single participation request row (teacher/admin lists).
 *
 * @param array<string, mixed> $req
 * @return array<string, mixed>
 */
final class ParticipationRequestRowPresenter
{
    public static function decorate(array $req): array
    {
        $s = (string) ($req['status'] ?? 'pending');
        $statusColor = $s === 'approved' ? '#22c55e' : ($s === 'rejected' ? '#ef4444' : '#f97316');
        $statusBg = $s === 'approved' ? '#f0fdf4' : ($s === 'rejected' ? '#fef2f2' : '#fff7ed');
        $studentName = (string) ($req['student_name_full'] ?? $req['student_name'] ?? 'Student');
        $firstLetter = strtoupper(substr($studentName, 0, 1));
        $processed = DisplayFormatter::shortDayMonth((string) ($req['updated_at'] ?? ''));

        return array_merge($req, [
            'display_status' => $s,
            'display_status_color' => $statusColor,
            'display_status_bg' => $statusBg,
            'display_student_name' => $studentName,
            'display_student_initial' => $firstLetter,
            'display_processed_short' => $processed !== '' ? $processed : 'Recently',
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    public static function decorateList(array $rows): array
    {
        return array_map(static fn(array $r): array => self::decorate($r), $rows);
    }
}

/**
 * Parses "QuantitÃ©: N" prefix from materiel resource details (controller-layer).
 */
final class ResourceMaterielPresenter
{
    /**
     * @return array{qty_badge: string, details_plain: string}
     */
    public static function splitQuantityPrefix(string $rawDetails): array
    {
        if (!str_starts_with($rawDetails, 'QuantitÃ©: ')) {
            return ['qty_badge' => '', 'details_plain' => trim($rawDetails)];
        }
        $lines = explode("\n", $rawDetails, 2);
        $qtyBadge = '';
        if (preg_match('/^QuantitÃ©: (\d+)/', $lines[0], $m)) {
            $qtyBadge = $m[1];
        }

        return ['qty_badge' => $qtyBadge, 'details_plain' => trim($lines[1] ?? '')];
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    public static function decorateItems(array $items): array
    {
        return array_map(static function (array $item): array {
            $split = self::splitQuantityPrefix((string) ($item['details'] ?? ''));

            return array_merge($item, [
                'materiel_qty_badge' => $split['qty_badge'],
                'materiel_details_plain' => $split['details_plain'],
            ]);
        }, $items);
    }
}

/**
 * Precomputes event card labels for teacher/admin listings (controllers call this).
 *
 * @param array<string, mixed> $e
 * @return array<string, mixed>
 */
final class EventCardPresenter
{
    public static function decorate(array $e): array
    {
        $eventDate = (string) ($e['event_date'] ?? '');
        $datePrimary = trim((string) ($e['date_debut'] ?? ''));
        if ($datePrimary === '' && $eventDate !== '') {
            $ts = strtotime($eventDate);
            $datePrimary = $ts !== false ? date('M d, Y', $ts) : 'N/A';
        } elseif ($datePrimary === '') {
            $datePrimary = 'N/A';
        }

        $timePrimary = trim((string) ($e['heure_debut'] ?? ''));
        if ($timePrimary === '' && $eventDate !== '') {
            $ts = strtotime($eventDate);
            $timePrimary = $ts !== false ? date('H:i', $ts) : '-';
        } elseif ($timePrimary === '') {
            $timePrimary = '-';
        }

        $approvedAt = (string) ($e['approved_at'] ?? '');
        $rejectionReason = (string) ($e['rejection_reason'] ?? 'No specific reason provided.');
        $approvedTs = $approvedAt !== '' ? strtotime($approvedAt) : false;
        $rejectionModalDateLabel = $approvedTs !== false ? date('d M Y \a\t H:i', $approvedTs) : 'Unknown date';

        $approval = strtolower((string) ($e['approval_status'] ?? 'approved'));
        if ($approval === 'pending') {
            $pillBg = '#fff7ed';
            $pillColor = '#f97316';
        } elseif ($approval === 'rejected') {
            $pillBg = '#fef2f2';
            $pillColor = '#ef4444';
        } else {
            $pillBg = '#f0fdf4';
            $pillColor = '#22c55e';
        }

        return array_merge($e, [
            'display_date_primary' => $datePrimary,
            'display_time_primary' => $timePrimary,
            'display_rejection_modal_reason_js' => htmlspecialchars(addslashes($rejectionReason), ENT_QUOTES, 'UTF-8'),
            'display_rejection_modal_date_js' => htmlspecialchars(addslashes($rejectionModalDateLabel), ENT_QUOTES, 'UTF-8'),
            'display_approval_lower' => $approval,
            'display_approval_pill_bg' => $pillBg,
            'display_approval_pill_color' => $pillColor,
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    public static function decorateList(array $rows): array
    {
        return array_map(static fn(array $row): array => self::decorate($row), $rows);
    }
}

/**
 * Student-facing event list/detail display rows (controllers only).
 */
final class StudentEventPresenter
{
    /**
     * @param array<string, mixed> $event
     * @return array<string, mixed>
     */
    public static function decorateListCard(array $event): array
    {
        $eventDate = (string) ($event['event_date'] ?? 'now');
        $ts = strtotime($eventDate !== '' ? $eventDate : 'now');
        $dateLine = trim((string) ($event['date_debut'] ?? ''));
        if ($dateLine === '') {
            $dateLine = $ts !== false ? date('Y-m-d', $ts) : '';
        }
        $heure = (string) ($event['heure_debut'] ?? '');
        $timeLine = $heure !== '' ? substr($heure, 0, 5) : ($ts !== false ? date('H:i', $ts) : '');

        return array_merge($event, [
            'display_date_line' => $dateLine,
            'display_time_line' => $timeLine,
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    public static function decorateListCards(array $rows): array
    {
        return array_map(static fn(array $r): array => self::decorateListCard($r), $rows);
    }

    /**
     * @param array<string, mixed> $p participation row
     */
    public static function decorateParticipationRow(array $p): array
    {
        $dd = (string) ($p['date_debut'] ?? '');
        $dateShown = $dd !== '' ? DisplayFormatter::shortMonthDate($dd) : 'N/A';
        $upd = DisplayFormatter::humanParticipationUpdated((string) ($p['p_update_date'] ?? ''));

        return array_merge($p, [
            'display_event_date' => $dateShown,
            'display_status_updated' => $upd,
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    public static function decorateParticipationRows(array $rows): array
    {
        return array_map(static fn(array $r): array => self::decorateParticipationRow($r), $rows);
    }
}

/**
 * Student group listing/detail presentation (covers, membership flags, member chips).
 */
final class GroupPresenter
{
    /**
     * @param array<string, mixed> $g
     * @return array<string, mixed>
     */
    public static function decorateListingRow(array $g, GroupeRepository $groupeRepository, int $viewerUserId, bool $withMembershipFlags): array
    {
        $g['cover_url'] = trim((string) ($g['image_url'] ?? $g['photo'] ?? $g['image'] ?? ''));
        if ($withMembershipFlags) {
            $groupId = (int) ($g['id_groupe'] ?? 0);
            $g['is_member_viewer'] = $groupId > 0 ? $groupeRepository->estMembre($groupId, $viewerUserId) : false;
            $ownerId = (int) ($g['id_createur'] ?? $g['created_by'] ?? 0);
            $g['is_owner_viewer'] = $ownerId === $viewerUserId;
        }

        return $g;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    public static function decorateListingRows(array $rows, GroupeRepository $groupeRepository, int $viewerUserId, bool $withMembershipFlags): array
    {
        return array_map(
            static fn (array $g): array => self::decorateListingRow($g, $groupeRepository, $viewerUserId, $withMembershipFlags),
            $rows
        );
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function detailCoverUrl(array $row): string
    {
        return trim((string) ($row['image_url'] ?? $row['photo'] ?? $row['image'] ?? ''));
    }

    /**
     * @param array<int, array<string, mixed>> $membres
     * @return array<int, array{display_name:string, avatar_initial:string, role_label:string}>
     */
    public static function formatMembers(array $membres): array
    {
        $out = [];
        foreach ($membres as $m) {
            $name = trim((string) ($m['name'] ?? ''));
            $out[] = [
                'display_name' => $name !== '' ? $name : 'Member',
                'avatar_initial' => $name !== '' ? strtoupper(substr($name, 0, 1)) : '?',
                'role_label' => (string) ($m['role'] ?? ''),
            ];
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $errorsByField
     * @return array<int, string>
     */
    public static function formatErrors(array $errorsByField): array
    {
        return DiscussionPresenter::flattenFieldErrors($errorsByField);
    }
}


