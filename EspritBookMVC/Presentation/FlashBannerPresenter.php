<?php

declare(strict_types=1);

/**
 * Normalizes flash messages for dumb templates (no $_SESSION in views).
 *
 * @return array{message: string, alert_class: string, inner_style: string}|null
 */
final class FlashBannerPresenter
{
    public static function fromSessionFlash(?array $flash): ?array
    {
        if ($flash === null || ($flash['message'] ?? '') === '') {
            return null;
        }
        $isError = ($flash['type'] ?? '') === 'error';

        return [
            'message' => (string) $flash['message'],
            'alert_class' => $isError ? 'danger' : 'success',
            'inner_style' => $isError
                ? 'background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.3); color: #dc3545;'
                : 'background: rgba(25, 135, 84, 0.1); border: 1px solid rgba(25, 135, 84, 0.3); color: #198754;',
        ];
    }
}
