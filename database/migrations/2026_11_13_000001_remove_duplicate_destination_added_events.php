<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('spk_events')) {
            return;
        }

        DB::statement(
            'DELETE duplicate FROM spk_events AS duplicate
             INNER JOIN spk_events AS check_in
                ON check_in.spk_id = duplicate.spk_id
                AND check_in.transport_service_id = duplicate.transport_service_id
                AND check_in.itinerary_stop_id = duplicate.itinerary_stop_id
                AND check_in.event_type = ?
             WHERE duplicate.event_type = ?',
            ['check_in', 'destination_added']
        );
    }

    public function down(): void
    {
        //
    }
};
