<?php

declare(strict_types=1);

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
