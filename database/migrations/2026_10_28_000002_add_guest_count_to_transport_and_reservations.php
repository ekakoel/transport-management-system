<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transport_services') && !Schema::hasColumn('transport_services', 'guest_count')) {
            Schema::table('transport_services', function (Blueprint $table) {
                $table->unsignedSmallInteger('guest_count')->default(1)->after('customer_phone');
            });
        }

        if (Schema::hasTable('reservations') && !Schema::hasColumn('reservations', 'guest_count')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->unsignedSmallInteger('guest_count')->default(1)->after('customer_phone');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('reservations') && Schema::hasColumn('reservations', 'guest_count')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn('guest_count');
            });
        }

        if (Schema::hasTable('transport_services') && Schema::hasColumn('transport_services', 'guest_count')) {
            Schema::table('transport_services', function (Blueprint $table) {
                $table->dropColumn('guest_count');
            });
        }
    }
};
