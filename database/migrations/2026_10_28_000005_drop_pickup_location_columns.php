<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transport_services') && Schema::hasColumn('transport_services', 'pickup_location')) {
            Schema::table('transport_services', function (Blueprint $table) {
                $table->dropColumn('pickup_location');
            });
        }

        if (Schema::hasTable('reservations') && Schema::hasColumn('reservations', 'pickup_location')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn('pickup_location');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transport_services') && !Schema::hasColumn('transport_services', 'pickup_location')) {
            Schema::table('transport_services', function (Blueprint $table) {
                $table->string('pickup_location', 255)->nullable();
            });
        }

        if (Schema::hasTable('reservations') && !Schema::hasColumn('reservations', 'pickup_location')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->string('pickup_location', 255)->nullable();
            });
        }
    }
};
