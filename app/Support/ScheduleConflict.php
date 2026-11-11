<?php

namespace App\Support;

use App\Models\TransportService;
use Carbon\CarbonImmutable;
use Closure;

class ScheduleConflict
{
    public const TIMEZONE = 'Asia/Makassar';

    /**
     * Jeda minimal setelah layanan selesai sebelum driver/kendaraan bisa
     * mengambil SPK berikutnya (persiapan, break, dan lain-lain).
     */
    public const PREP_BUFFER_HOURS = 2;

    /**
     * Cari layanan yang bentrok untuk driver pada rentang waktu tertentu.
     */
    public static function findDriverConflict(
        int $driverId,
        string $serviceDate,
        string $serviceEndDate,
        string $startTime,
        string $endTime
    ): ?TransportService {
        return self::find(
            $serviceDate,
            $serviceEndDate,
            $startTime,
            $endTime,
            fn ($query) => $query->where('driver_id', $driverId)
        );
    }

    /**
     * Cari layanan yang bentrok untuk kendaraan pada rentang waktu tertentu.
     */
    public static function findVehicleConflict(
        int $vehicleId,
        string $serviceDate,
        string $serviceEndDate,
        string $startTime,
        string $endTime
    ): ?TransportService {
        return self::find(
            $serviceDate,
            $serviceEndDate,
            $startTime,
            $endTime,
            fn ($query) => $query->where('vehicle_id', $vehicleId)
        );
    }

    public static function describe(TransportService $service): string
    {
        $startDate = $service->service_date->format('d/m/Y');
        $endDate = $service->service_end_date->format('d/m/Y');
        $startTime = self::timeLabel($service->start_time);
        $endTime = self::timeLabel($service->end_time);

        if ($startDate === $endDate) {
            return "{$startDate} pukul {$startTime}-{$endTime} WITA";
        }

        return "{$startDate} pukul {$startTime} s.d. {$endDate} pukul {$endTime} WITA";
    }

    /**
     * Aturan bentrok:
     * - Beberapa SPK dalam satu hari diperbolehkan selama rentang waktunya tidak overlap.
     * - Setelah layanan selesai, driver/kendaraan mendapat jeda PREP_BUFFER_HOURS
     *   sebelum bisa mengambil SPK berikutnya.
     * - Layanan multi-hari dianggap satu rentang waktu kontinu.
     * - Data lama tanpa start_time/end_time dianggap memakai seluruh hari.
     */
    private static function find(
        string $serviceDate,
        string $serviceEndDate,
        string $startTime,
        string $endTime,
        Closure $resource
    ): ?TransportService {
        $windowStart = self::dateTime($serviceDate, $startTime, '00:00');
        $windowEnd = self::dateTime($serviceEndDate, $endTime, '23:59');

        if ($windowEnd->lessThanOrEqualTo($windowStart)) {
            $windowEnd = $windowStart->endOfDay();
        } else {
            $windowEnd = $windowEnd->addHours(self::PREP_BUFFER_HOURS);
        }

        $query = TransportService::query()
            ->whereNotIn('status', ['Cancelled', 'Expired', 'Completed'])
            ->whereDate('service_date', '<=', $serviceEndDate)
            ->whereDate('service_end_date', '>=', $serviceDate);

        $resource($query);

        return $query->get()
            ->first(fn (TransportService $service) => self::overlaps($service, $windowStart, $windowEnd));
    }

    private static function overlaps(TransportService $service, CarbonImmutable $start, CarbonImmutable $end): bool
    {
        if (blank($service->start_time) || blank($service->end_time)) {
            return true;
        }

        $existingStart = self::dateTime(
            $service->service_date->format('Y-m-d'),
            (string) $service->start_time,
            '00:00'
        );

        $existingEnd = self::dateTime(
            $service->service_end_date->format('Y-m-d'),
            (string) $service->end_time,
            '23:59'
        );

        return $existingStart->lessThan($end)
            && $existingEnd->addHours(self::PREP_BUFFER_HOURS)->greaterThan($start);
    }

    private static function dateTime(string $date, string $time, string $fallback): CarbonImmutable
    {
        $time = substr(trim($time), 0, 5);

        if (! preg_match('/^\d{2}:\d{2}$/', $time)) {
            $time = $fallback;
        }

        return CarbonImmutable::createFromFormat('Y-m-d H:i', $date . ' ' . $time, self::TIMEZONE);
    }

    private static function timeLabel(?string $time): string
    {
        $time = substr(trim((string) $time), 0, 5);

        return preg_match('/^\d{2}:\d{2}$/', $time) ? $time : '-';
    }
}
