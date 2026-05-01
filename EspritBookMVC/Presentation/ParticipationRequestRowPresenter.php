<?php

declare(strict_types=1);

require_once __DIR__ . '/DisplayFormatter.php';

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
