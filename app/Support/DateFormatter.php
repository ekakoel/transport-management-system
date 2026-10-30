<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class DateFormatter
{
    public static function format($date, $time = null, string $dateFormat = 'd M Y', string $timeFormat = 'H:i'): string
    {
        if ($date === null || $date === '') {
            return 'Belum diatur';
        }

        $formattedDate = static::toCarbon($date)->format($dateFormat);

        if ($time === null || $time === '') {
            return $formattedDate;
        }

        return $formattedDate . ' (' . static::toCarbon($time)->format($timeFormat) . ')';
    }

    private static function toCarbon($value): Carbon
    {
        if ($value instanceof CarbonInterface) {
            return $value->copy();
        }

        return Carbon::parse($value);
    }
}
