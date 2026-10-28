<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('transport_services')) {
            Schema::table('transport_services', function (Blueprint $table) {
                if (!Schema::hasColumn('transport_services', 'reservation_user_id')) {
                    $table->foreignId('reservation_user_id')->nullable()->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('transport_services', 'reservation_id')) {
                    $table->foreignId('reservation_id')->nullable()->constrained('reservations')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('reservations') && !Schema::hasTable('reservation_guests')) {
            Schema::create('reservation_guests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
                $table->string('name', 150);
                $table->enum('gender', ['Male', 'Female']);
                $table->enum('age_category', ['Adult', 'Children']);
                $table->string('phone', 40)->nullable();
                $table->timestamps();
                $table->index('reservation_id');
            });
        }

        if (Schema::hasTable('transport_services') && !Schema::hasTable('service_guests')) {
            Schema::create('service_guests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('transport_service_id')->constrained('transport_services')->cascadeOnDelete();
                $table->string('name', 150);
                $table->enum('gender', ['Male', 'Female']);
                $table->enum('age_category', ['Adult', 'Children']);
                $table->string('phone', 40)->nullable();
                $table->timestamps();
                $table->index('transport_service_id');
            });
        }
    }

    public function down(): void
    {
        // Intentionally preserve operational and guest data during rollback.
    }
};
