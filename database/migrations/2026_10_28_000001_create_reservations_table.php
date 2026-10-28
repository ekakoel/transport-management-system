<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_number', 40)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_type_id')->constrained('service_types');
            $table->string('customer_name', 150);
            $table->string('customer_phone', 40)->nullable();
            $table->unsignedSmallInteger('guest_count')->default(1);
            $table->date('service_date');
            $table->date('service_end_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('itinerary')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('Pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('transport_service_id')->nullable()->unique()->constrained('transport_services')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'service_date']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
