<?php

declare(strict_types=1);

require_once __DIR__ . '/DisplayFormatter.php';

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
