<?php

declare(strict_types=1);

/**
 * Date/text formatting for views — called from controllers only.
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
