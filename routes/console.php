<?php
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use App\Models\SpkEvent;
use App\Models\TransportService;

Artisan::command('tms:about', function () { $this->info('Bali Kami TMS'); });

Schedule::call(function () {
    $today = now('Asia/Makassar')->toDateString();

    TransportService::query()
        ->with('spk')
        ->whereDate('service_end_date', '<', $today)
        ->whereNotIn('status', ['Completed', 'Cancelled'])
        ->chunkById(100, function ($services) {
            foreach ($services as $service) {
                DB::transaction(function () use ($service) {
                    $service->update(['status' => 'Completed']);

                    if ($service->spk) {
                        SpkEvent::firstOrCreate(
                            ['spk_id' => $service->spk->id, 'event_type' => 'service_completed'],
                            [
                                'transport_service_id' => $service->id,
                                'actor_type' => 'system',
                                'created_at' => now('UTC'),
                                'metadata' => ['automatic' => true],
                            ]
                        );
                    }
                });
            }
        });
})->hourly()->name('complete-past-transport-services')->withoutOverlapping();
