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

        DB::table('transport_services')
            ->whereNotNull('reservation_user_id')
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('reservations')
                    ->whereColumn('reservations.transport_service_id', 'transport_services.id');
            })
            ->orderBy('id')
            ->get()
            ->each(function ($service) {
                $stops = DB::table('service_itinerary_stops')
                    ->where('transport_service_id', $service->id)
                    ->orderBy('sort_order')
                    ->get(['name', 'visit_time', 'google_maps_url', 'address'])
                    ->map(fn ($stop) => (array) $stop)
                    ->values()
                    ->all();
                $userName = DB::table('users')->where('id', $service->reservation_user_id)->value('name');

                $reservationId = DB::table('reservations')->insertGetId([
                    'reservation_number' => 'RSV-SVC-' . $service->id,
                    'user_id' => $service->reservation_user_id,
                    'service_type_id' => $service->service_type_id,
                    'customer_name' => $service->customer_name ?: $userName ?: 'Reservation',
                    'customer_phone' => $service->customer_phone,
                    'guest_count' => $service->guest_count ?: 1,
                    'service_date' => $service->service_date,
                    'service_end_date' => $service->service_end_date,
                    'start_time' => $service->start_time,
                    'end_time' => $service->end_time,
                    'itinerary' => json_encode($stops, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'notes' => $service->notes,
                    'status' => $service->status === 'Cancelled' ? 'Cancelled' : 'Approved',
                    'transport_service_id' => $service->id,
                    'reviewed_by' => $service->created_by,
                    'reviewed_at' => $service->created_at,
                    'created_at' => $service->created_at,
                    'updated_at' => $service->updated_at,
                ]);
                DB::table('transport_services')->where('id', $service->id)->update(['reservation_id' => $reservationId]);

                DB::table('service_guests')
                    ->where('transport_service_id', $service->id)
                    ->orderBy('id')
                    ->get()
                    ->each(function ($guest) use ($reservationId) {
                        DB::table('reservation_guests')->insert([
                            'reservation_id' => $reservationId,
                            'name' => $guest->name,
                            'gender' => $guest->gender,
                            'age_category' => $guest->age_category,
                            'phone' => $guest->phone,
                            'created_at' => $guest->created_at,
                            'updated_at' => $guest->updated_at,
                        ]);
                    });
            });
    }

    public function down(): void
    {
        DB::table('reservations')->where('reservation_number', 'like', 'RSV-SVC-%')->delete();
    }
};
