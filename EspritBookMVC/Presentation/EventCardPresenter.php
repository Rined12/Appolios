<?php

declare(strict_types=1);

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
            $timePrimary = $ts !== false ? date('H:i', $ts) : '—';
        } elseif ($timePrimary === '') {
            $timePrimary = '—';
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
