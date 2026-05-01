<?php

declare(strict_types=1);

/**
 * Parses "Quantité: N" prefix from materiel resource details (controller-layer).
 */
final class ResourceMaterielPresenter
{
    /**
     * @return array{qty_badge: string, details_plain: string}
     */
    public static function splitQuantityPrefix(string $rawDetails): array
    {
        if (!str_starts_with($rawDetails, 'Quantité: ')) {
            return ['qty_badge' => '', 'details_plain' => trim($rawDetails)];
        }
        $lines = explode("\n", $rawDetails, 2);
        $qtyBadge = '';
        if (preg_match('/^Quantité: (\d+)/', $lines[0], $m)) {
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
