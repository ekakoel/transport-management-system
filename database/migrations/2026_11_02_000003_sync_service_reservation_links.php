<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transport_services') || !Schema::hasTable('reservations')) {
            return;
        }

        DB::table('reservations')
            ->whereNotNull('transport_service_id')
            ->orderBy('id')
            ->get(['id', 'transport_service_id'])
            ->each(function ($reservation) {
                DB::table('transport_services')
                    ->where('id', $reservation->transport_service_id)
                    ->update(['reservation_id' => $reservation->id]);
            });
    }

    public function down(): void
    {
        // The relationship remains valid through reservations.transport_service_id.
    }
};
