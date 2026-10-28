<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DummyTransportServiceSeeder extends Seeder
{
    public function run(): void
    {
        $driver = DB::table('drivers')->orderBy('id')->first();
        $vehicle = DB::table('vehicles')->orderBy('id')->first();
        $user = DB::table('users')->where('is_active', 1)->orderBy('id')->first();
        $contact = DB::table('emergency_contacts')
            ->where('is_active', 1)
            ->orderByDesc('is_default')
            ->first();

        if (!$driver || !$vehicle) {
            throw new \RuntimeException(
                'Data driver atau kendaraan belum tersedia. Tambahkan keduanya terlebih dahulu.'
            );
        }

        $types = DB::table('service_types')
            ->where('is_active', 1)
            ->pluck('id', 'name');

        $samples = [
            [
                'code' => 'DUMMY-TMS-001',
                'customer' => 'John Smith',
                'phone' => '+628123450001',
                'type' => 'Airport Transfer',
                'day' => 1,
                'start' => '09:00',
                'end' => '10:30',
                'pickup' => 'Ngurah Rai International Airport',
                'status' => 'Confirmed',
                'notes' => 'Airport pickup to hotel in Nusa Dua.',
                'stops' => [
                    ['Ngurah Rai International Airport', 'Kuta, Bali'],
                    ['Hotel in Nusa Dua', 'Nusa Dua, Bali'],
                ],
            ],
            [
                'code' => 'DUMMY-TMS-002',
                'customer' => 'Emily Johnson',
                'phone' => '+628123450002',
                'type' => 'Point to Point',
                'day' => 2,
                'start' => '10:00',
                'end' => '11:00',
                'pickup' => 'Seminyak',
                'status' => 'Draft',
                'notes' => 'Transfer from Seminyak to Ubud.',
                'stops' => [
                    ['Seminyak Pickup Point', 'Seminyak, Bali'],
                    ['Ubud Center', 'Ubud, Bali'],
                ],
            ],
            [
                'code' => 'DUMMY-TMS-003',
                'customer' => 'Michael Chen',
                'phone' => '+628123450003',
                'type' => 'Hourly Charter',
                'day' => 3,
                'start' => '09:00',
                'end' => '13:00',
                'pickup' => 'Sanur Hotel',
                'status' => 'Assigned',
                'notes' => 'Four-hour private charter.',
                'stops' => [
                    ['Sanur Hotel', 'Sanur, Bali'],
                    ['Bajra Sandhi Monument', 'Denpasar, Bali'],
                    ['Sanur Hotel', 'Sanur, Bali'],
                ],
            ],
            [
                'code' => 'DUMMY-TMS-004',
                'customer' => 'Sarah Williams',
                'phone' => '+628123450004',
                'type' => 'Full Day Charter',
                'day' => 4,
                'start' => '08:00',
                'end' => '18:00',
                'pickup' => 'Ubud Hotel',
                'status' => 'Confirmed',
                'notes' => 'Full-day private transportation.',
                'stops' => [
                    ['Ubud Hotel', 'Ubud, Bali'],
                    ['Tegallalang Rice Terrace', 'Tegallalang, Bali'],
                    ['Tirta Empul Temple', 'Tampaksiring, Bali'],
                    ['Ubud Hotel', 'Ubud, Bali'],
                ],
            ],
            [
                'code' => 'DUMMY-TMS-005',
                'customer' => 'David Lee',
                'phone' => '+628123450005',
                'type' => 'Tour Transport',
                'day' => 5,
                'start' => '08:30',
                'end' => '16:30',
                'pickup' => 'Kuta Hotel',
                'status' => 'Draft',
                'notes' => 'South Bali sightseeing tour.',
                'stops' => [
                    ['Kuta Hotel', 'Kuta, Bali'],
                    ['Uluwatu Temple', 'Pecatu, Bali'],
                    ['Padang Padang Beach', 'Pecatu, Bali'],
                    ['Kuta Hotel', 'Kuta, Bali'],
                ],
            ],
        ];

        DB::transaction(function () use (
            $samples, $types, $driver, $vehicle, $user, $contact
        ) {
            foreach ($samples as $sample) {
                if (!isset($types[$sample['type']])) {
                    throw new \RuntimeException(
                        'Service type tidak ditemukan: ' . $sample['type']
                    );
                }

                $date = Carbon::today()->addDays($sample['day']);
                $now = now();

                $serviceId = DB::table('transport_services')
                    ->where('service_code', $sample['code'])
                    ->value('id');

                $serviceData = [
                    'service_code' => $sample['code'],
                    'customer_name' => $sample['customer'],
                    'customer_phone' => $sample['phone'],
                    'service_type_id' => $types[$sample['type']],
                    'service_date' => $date->toDateString(),
                    'service_end_date' => $date->toDateString(),
                    'start_time' => $sample['start'] . ':00',
                    'end_time' => $sample['end'] . ':00',
                    'pickup_location' => $sample['pickup'],
                    'notes' => $sample['notes'],
                    'driver_id' => $driver->id,
                    'vehicle_id' => $vehicle->id,
                    'status' => $sample['status'],
                    'created_by' => $user?->id,
                    'updated_at' => $now,
                ];

                if ($serviceId) {
                    DB::table('transport_services')
                        ->where('id', $serviceId)
                        ->update($serviceData);
                } else {
                    $serviceData['created_at'] = $now;
                    $serviceId = DB::table('transport_services')
                        ->insertGetId($serviceData);
                }

                DB::table('service_itinerary_stops')
                    ->where('transport_service_id', $serviceId)
                    ->delete();

                foreach ($sample['stops'] as $index => $stop) {
                    DB::table('service_itinerary_stops')->insert([
                        'transport_service_id' => $serviceId,
                        'name' => $stop[0],
                        'address' => $stop[1],
                        'sort_order' => $index + 1,
                        'status' => 'Planned',
                        'added_by_driver' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                $spk = DB::table('spks')
                    ->where('transport_service_id', $serviceId)
                    ->first();

                if (!$spk) {
                    DB::table('spks')->insert([
                        'transport_service_id' => $serviceId,
                        'spk_number' => 'SPK-' . $sample['code'],
                        'token_hash' => hash('sha256', Str::random(64)),
                        'published_at' => null,
                        'emergency_name' => $contact?->name,
                        'emergency_phone' => $contact?->phone,
                        'emergency_whatsapp' => $contact?->whatsapp,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        });

        $this->command->info('Berhasil membuat atau memperbarui 5 data dummy service.');
        $this->command->info('SPK dibuat dalam status belum dipublikasikan.');
    }
}
