<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('service_itinerary_stops')) {
            return;
        }

        Schema::table('service_itinerary_stops', function (Blueprint $table) {
            if (!Schema::hasColumn('service_itinerary_stops', 'visit_time')) {
                $table->time('visit_time')->nullable()->after('name');
            }
            if (!Schema::hasColumn('service_itinerary_stops', 'google_maps_url')) {
                $table->string('google_maps_url', 500)->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        // Preserve itinerary data during rollback; these fields are additive.
    }
};
