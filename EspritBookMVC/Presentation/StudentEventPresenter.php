<?php

declare(strict_types=1);

require_once __DIR__ . '/DisplayFormatter.php';

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
