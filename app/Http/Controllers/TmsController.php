<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\EmergencyContact;
use App\Models\ItineraryStop;
use App\Models\Reservation;
use App\Models\ServiceGuest;
use App\Models\ServiceType;
use App\Models\Spk;
use App\Models\SpkEvent;
use App\Models\TransportService;
use App\Models\Vehicle;
use App\Models\User;
use App\Support\DateFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TmsController extends Controller
{
    public function dashboard()
    {
        if (strtolower((string) auth()->user()->role) === 'reservation') return redirect()->route('reservations.index');
        abort_unless(in_array(strtolower((string) auth()->user()->role), ['admin', 'owner', 'manager'], true), 403);
        $today = now('Asia/Makassar')->toDateString();

        $todayReservations = Reservation::query()
            ->whereDate('service_date', '<=', $today)
            ->whereDate('service_end_date', '>=', $today)
            ->whereIn('status', ['Pending', 'Approved']);

        $todayReservationCount = (clone $todayReservations)->count();
        $todayPendingReservationCount = (clone $todayReservations)->where('status', 'Pending')->count();

        $todayActiveServices = TransportService::query()
            ->whereDate('service_date', '<=', $today)
            ->whereDate('service_end_date', '>=', $today)
            ->whereIn('status', ['Published', 'In Progress'])
            ->get(['id', 'status', 'driver_id', 'vehicle_id']);

        $todayActiveServiceCount = $todayActiveServices->count();
        $todayInProgressServiceCount = $todayActiveServices->where('status', 'In Progress')->count();
        $activeVehicleCount = $todayActiveServices->pluck('vehicle_id')->filter()->unique()->count();
        $activeDriverCount = $todayActiveServices->pluck('driver_id')->filter()->unique()->count();

        return view('dashboard', [
            'todayReservationCount' => $todayReservationCount,
            'todayPendingReservationCount' => $todayPendingReservationCount,
            'todayActiveServiceCount' => $todayActiveServiceCount,
            'todayInProgressServiceCount' => $todayInProgressServiceCount,
            'activeVehicleCount' => $activeVehicleCount,
            'activeDriverCount' => $activeDriverCount,
            'services' => TransportService::with(['driver', 'vehicle', 'serviceType', 'spk'])
                ->whereDate('service_end_date', '>=', $today)
                ->whereIn('status', ['Published', 'In Progress'])
                ->orderBy('service_date')
                ->orderBy('start_time')
                ->limit(8)
                ->get()
        ]);
    }

    public function services(Request $request)
    {
        $today = now('Asia/Makassar')->toDateString();

        $q = TransportService::with(['driver', 'vehicle', 'serviceType', 'spk']);
        if ($request->filled('date')) $q->whereDate('service_date', $request->input('date'));
        if ($request->filled('status')) $q->where('status', $request->input('status'));
        if ($request->filled('q')) $q->where(function ($w) use ($request) {
            $term = $request->input('q');
            $w->where('service_code', 'like', "%$term%")->orWhere('customer_name', 'like', "%$term%");
        });

        $activeServices = (clone $q)
            ->whereDate('service_end_date', '>=', $today)
            ->whereNotIn('status', ['Completed', 'Cancelled', 'Expired'])
            ->orderBy('service_date')
            ->orderBy('start_time')
            ->paginate(15, ['*'], 'active_page')
            ->withQueryString();

        $historyServices = (clone $q)
            ->where(function ($query) use ($today) {
                $query->whereDate('service_end_date', '<', $today)
                    ->orWhereIn('status', ['Completed', 'Cancelled', 'Expired']);
            })
            ->orderByDesc('service_date')
            ->orderByDesc('start_time')
            ->paginate(15, ['*'], 'history_page')
            ->withQueryString();

        $serviceStatusCounts = TransportService::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $upcomingServiceCount = TransportService::whereDate('service_date', '>=', $today)
            ->whereNotIn('status', ['Cancelled', 'Expired', 'Completed'])
            ->count();
        $totalServiceCount = $activeServices->total() + $historyServices->total();

        return view('services.index', compact('activeServices', 'historyServices', 'serviceStatusCounts', 'upcomingServiceCount', 'totalServiceCount'));
    }

    public function createService()
    {
        return view('services.create', [
            'drivers' => Driver::where('is_active', 1)->orderBy('name')->get(),
            'vehicles' => Vehicle::where('status', 'Available')->orderBy('name')->get(),
            'types' => ServiceType::where('is_active', 1)->orderBy('name')->get(),
            'contacts' => EmergencyContact::where('is_active', 1)->orderByDesc('is_default')->get(),
            'reservationUsers' => User::where('role', 'reservation')->where('is_active', 1)->orderBy('name')->get()
        ]);
    }

    public function storeService(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')->where(fn($q) => $q->where('role', 'reservation')->where('is_active', 1))],
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'nullable|string|max:40',
            'guests' => 'nullable|array|max:100',
            'guests.*.name' => 'required_with:guests.*.gender|string|max:150',
            'guests.*.gender' => ['required_with:guests.*.name', Rule::in(['Male', 'Female'])],
            'guests.*.age_category' => ['required_with:guests.*.name', Rule::in(['Adult', 'Children'])],
            'guests.*.phone' => 'nullable|string|max:40',
            'service_type_id' => 'required|exists:service_types,id',
            'service_date' => 'required|date',
            'service_end_date' => 'required|date|after_or_equal:service_date',
            'driver_id' => 'required|exists:drivers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'notes' => 'nullable|string|max:2000',
            'emergency_contact_id' => 'nullable|exists:emergency_contacts,id',
            'stops' => 'required|array|min:1|max:40',
            'stops.*.name' => 'required|string|max:150',
            'stops.*.visit_time' => 'required|date_format:H:i',
            'stops.*.google_maps_url' => ['required', 'url', 'max:500', 'regex:#^https?://(maps\.app\.goo\.gl|goo\.gl|www\.google\.com|maps\.google\.com)/#i'],
            'stops.*.address' => 'nullable|string|max:500'
        ]);
        $stops = array_values($data['stops']);
        if ($data['service_date'] < now('Asia/Makassar')->toDateString()) return back()->withInput()->withErrors(['service_date' => 'Tanggal layanan tidak boleh di masa lalu.']);
        $conflict = $this->hasScheduleConflict((int)$data['driver_id'], (int)$data['vehicle_id'], $data['service_date'], $data['service_end_date']);
        if ($conflict) return back()->withInput()->withErrors(['driver_id' => 'Driver atau kendaraan sudah memiliki layanan yang tanggalnya bertumpang tindih.']);
        $rawToken = Str::random(64);
        $service = DB::transaction(function () use ($data, $stops, $rawToken, $request) {
            $service = TransportService::create([
                'service_code' => 'TMS-' . now('Asia/Makassar')->format('ymd') . '-' . Str::upper(Str::random(5)),
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'guest_count' => max(1, count(array_filter($data['guests'] ?? [], fn($g) => !empty($g['name'])))),
                'service_type_id' => $data['service_type_id'],
                'service_date' => $data['service_date'],
                'service_end_date' => $data['service_end_date'],
                'start_time' => $stops[0]['visit_time'],
                'end_time' => $stops[count($stops) - 1]['visit_time'],
                'notes' => $data['notes'] ?? null,
                'driver_id' => $data['driver_id'],
                'vehicle_id' => $data['vehicle_id'],
                'status' => 'Published',
                'created_by' => $request->user()->id
            ]);
            foreach (($data['guests'] ?? []) as $guest) if (!empty($guest['name'])) $service->guests()->create($guest);
            foreach ($stops as $i => $stop) if (!empty($stop['name'])) $service->stops()->create(['name' => $stop['name'], 'visit_time' => $stop['visit_time'], 'address' => $stop['address'] ?? null, 'google_maps_url' => $stop['google_maps_url'], 'sort_order' => $i + 1, 'status' => 'Planned', 'added_by_driver' => false]);
            $reservation = Reservation::create([
                'reservation_number' => 'RSV-' . now('Asia/Makassar')->format('ymd') . '-' . Str::upper(Str::random(6)),
                'user_id' => $data['user_id'],
                'service_type_id' => $data['service_type_id'],
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'guest_count' => $service->guest_count,
                'service_date' => $data['service_date'],
                'service_end_date' => $data['service_end_date'],
                'start_time' => $service->start_time,
                'end_time' => $service->end_time,
                'itinerary' => $stops,
                'notes' => $data['notes'] ?? null,
                'status' => 'Approved',
                'transport_service_id' => $service->id,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now('UTC'),
            ]);
            $service->update(['reservation_id' => $reservation->id]);
            foreach (($data['guests'] ?? []) as $guest) if (!empty($guest['name'])) $reservation->guests()->create($guest);
            $contact = isset($data['emergency_contact_id']) ? EmergencyContact::find($data['emergency_contact_id']) : EmergencyContact::where('is_active', 1)->where('is_default', 1)->first();
            $spk = Spk::create(['transport_service_id' => $service->id, 'spk_number' => 'SPK-' . now('Asia/Makassar')->format('Ymd') . '-' . str_pad((string)$service->id, 5, '0', STR_PAD_LEFT), 'token_hash' => hash('sha256', $rawToken), 'published_at' => now(), 'emergency_name' => $contact?->name ?? 'Operasional Bali Kami Tour', 'emergency_phone' => $contact?->phone, 'emergency_whatsapp' => $contact?->whatsapp]);
            session()->flash('new_spk_url', route('public.spk.number.show', [$spk->id, $spk->spk_number]));
            return $service;
        });
        return redirect()->route('services.show', $service)->with('success', 'Layanan dan SPK berhasil diterbitkan. Simpan tautan publik SPK yang ditampilkan satu kali.');
    }

    private function hasScheduleConflict(int $driverId, int $vehicleId, string $start, string $end): bool
    {
        return TransportService::whereNotIn('status', ['Cancelled', 'Expired'])
            ->where(function ($q) use ($driverId, $vehicleId) {
                $q->where('driver_id', $driverId)->orWhere('vehicle_id', $vehicleId);
            })
            ->whereDate('service_date', '<=', $end)->whereDate('service_end_date', '>=', $start)->exists();
    }

    public function showService(TransportService $service)
    {
        $service->load(['driver', 'vehicle', 'serviceType', 'stops', 'spk.events.stop', 'reservation.requester', 'guests']);
        $newUrl = $service->spk
            ? route('public.spk.number.show', [$service->spk->id, $service->spk->spk_number])
            : null;
        $spkSentEvents = $service->spk?->events->where('event_type', 'spk_sent_to_driver') ?? collect();
        $spkSentCount = $spkSentEvents->count();
        $infoSentEvents = $service->spk?->events->where('event_type', 'service_info_sent_to_reservation') ?? collect();
        $infoSentCount = $infoSentEvents->count();

        return view('services.show', compact('service', 'newUrl', 'spkSentCount', 'infoSentCount'));
    }

    public function sendSpkToDriver(TransportService $service)
    {
        if ($service->isLocked()) {
            return back()->withErrors(['spk' => 'SPK tidak dapat dikirim karena layanan sudah berlalu atau selesai.']);
        }

        $service->load(['driver', 'spk']);
        abort_unless($service->spk, 404, 'SPK tidak ditemukan.');

        $phone = preg_replace('/\D+/', '', (string) $service->driver?->phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }

        if (strlen($phone) < 8) {
            return back()->withErrors(['driver_phone' => 'Nomor WhatsApp driver belum tersedia atau tidak valid.']);
        }

        $publicUrl = route('public.spk.number.show', [$service->spk->id, $service->spk->spk_number]);
        $serviceDate = \App\Support\DateFormatter::format($service->service_date);
        $message = implode("\n", [
            'Halo ' . $service->driver->name . ',',
            '',
            'SPK untuk tanggal "' . $serviceDate . '" telah diterbitkan pada link berikut:',
            '',
            $publicUrl,
            '',
            '- Pastikan kondisi kendaraan dalam keadaan bersih, aman, dan siap digunakan.',
            '- Tiba tepat waktu sesuai jadwal penjemputan yang telah ditentukan.',
            '- Selalu mengemudi dengan baik, hati-hati, dan mematuhi rambu lalu lintas.',
            '- Pastikan kenyamanan penumpang selalu menjadi prioritas utama.',
            '- Selalu lakukan Check-in di lokasi destinasi sesuai ketentuan yang tertera pada SPK.',
            '- Jaga sikap profesional, ramah, dan menjaga nama baik perusahaan.',
            '',
            'Terima kasih,',
            'Bali Kami Tour',
        ]);

        SpkEvent::create([
            'spk_id' => $service->spk->id,
            'transport_service_id' => $service->id,
            'event_type' => 'spk_sent_to_driver',
            'actor_type' => 'user',
            'actor_id' => request()->user()?->id,
            'created_at' => now('UTC'),
            'metadata' => ['channel' => 'whatsapp', 'driver_phone' => $phone],
        ]);

        return redirect()->away('https://wa.me/' . $phone . '?text=' . rawurlencode($message));
    }

    public function sendServiceInfoToReservation(TransportService $service)
    {
        if ($service->isLocked()) {
            return back()->withErrors(['info' => 'Informasi layanan tidak dapat dikirim karena layanan sudah berlalu atau selesai.']);
        }

        $service->load(['driver', 'vehicle', 'serviceType', 'stops', 'spk', 'reservation.requester', 'guests']);
        abort_unless($service->spk, 404, 'SPK tidak ditemukan.');

        $reservation = $service->reservation;
        abort_unless($reservation, 404, 'Data reservasi tidak ditemukan.');
        $recipientName = $reservation?->requester?->name
            ?? $reservation?->customer_name
            ?? $service->customer_name
            ?? 'Bapak/Ibu';
        $phone = preg_replace('/\D+/', '', (string) ($reservation?->requester?->phone ?: $reservation?->customer_phone ?: $service->customer_phone));
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }

        if (strlen($phone) < 8) {
            return back()->withErrors(['reservation_phone' => 'Nomor WhatsApp reservasi belum tersedia atau tidak valid.']);
        }

        $guestLines = $service->guests->isNotEmpty()
            ? $service->guests->map(fn ($guest) => '- ' . $guest->name)->all()
            : ['- Tidak tersedia'];
        $destinationLines = $service->stops->isNotEmpty()
            ? $service->stops->map(function ($stop) {
                $time = $stop->visit_time ? \App\Support\DateFormatter::format($stop->visit_time, null, 'H:i') . ' WITA - ' : '';
                return '- ' . $time . $stop->name . ($stop->address ? ' (' . $stop->address . ')' : '');
            })->all()
            : ['- Tidak tersedia'];
        $detailUrl = route('reservations.show', $reservation);

        $lines = [
            'Halo ' . $recipientName . ',', '', 'Your order',
            'Order Number: ' . ($reservation?->reservation_number ?? $service->service_code),
            'Date: ' . \App\Support\DateFormatter::format($service->service_date),
            'Type: ' . ($service->serviceType?->name ?? 'Layanan Transportasi'), '',
            'Guest Name:', ...$guestLines, '',
            'Destination:', ...$destinationLines, '',
            'Driver: ' . ($service->driver?->name ?? '-'),
            'Hp: ' . ($service->driver?->phone ?? '-'),
            'Vehicle: ' . ($service->vehicle?->name ?? '-'),
            'Police Number: ' . ($service->vehicle?->plate_number ?? '-'), '',
            'Untuk melihat detail SPK, gunakan link berikut:',
            $detailUrl, '', 'Terima kasih,', 'online.balikamitour.com',
        ];

        SpkEvent::create([
            'spk_id' => $service->spk->id,
            'transport_service_id' => $service->id,
            'event_type' => 'service_info_sent_to_reservation',
            'actor_type' => 'user',
            'actor_id' => request()->user()?->id,
            'created_at' => now('UTC'),
            'metadata' => ['channel' => 'whatsapp', 'recipient_phone' => $phone],
        ]);

        return redirect()->away('https://wa.me/' . $phone . '?text=' . rawurlencode(implode("\n", $lines)));
    }

    public function previewSpk(TransportService $service)
    {
        $service->loadMissing('spk');
        abort_unless($service->spk, 404);

        return $this->showService($service);
    }

    public function storeGuest(Request $request, TransportService $service)
    {
        if (! $this->serviceChangesAllowed($service)) {
            return back()->withErrors(['guest' => 'Daftar tamu tidak dapat diubah karena tanggal layanan sudah terlewat.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'gender' => ['required', Rule::in(['Male', 'Female'])],
            'age_category' => ['required', Rule::in(['Adult', 'Children'])],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);

        $service->guests()->create($data);
        $service->update(['guest_count' => max(1, $service->guests()->count())]);

        return back()->with('success', 'Tamu berhasil ditambahkan.');
    }

    public function updateGuest(Request $request, TransportService $service, ServiceGuest $guest)
    {
        abort_unless((int) $guest->transport_service_id === (int) $service->id, 404);

        if (! $this->serviceChangesAllowed($service)) {
            return back()->withErrors(['guest' => 'Daftar tamu tidak dapat diubah karena tanggal layanan sudah terlewat.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'gender' => ['required', Rule::in(['Male', 'Female'])],
            'age_category' => ['required', Rule::in(['Adult', 'Children'])],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);

        $guest->update($data);

        return back()->with('success', 'Data tamu berhasil diperbarui.');
    }

    public function destroyGuest(TransportService $service, ServiceGuest $guest)
    {
        abort_unless((int) $guest->transport_service_id === (int) $service->id, 404);

        if (! $this->serviceChangesAllowed($service)) {
            return back()->withErrors(['guest' => 'Daftar tamu tidak dapat diubah karena tanggal layanan sudah terlewat.']);
        }

        $guest->delete();
        $service->update(['guest_count' => max(1, $service->guests()->count())]);

        return back()->with('success', 'Tamu berhasil dihapus.');
    }

    private function serviceChangesAllowed(TransportService $service): bool
    {
        return ! $service->isLocked();
    }

    public function storeStop(Request $request, TransportService $service)
    {
        if (! $this->serviceChangesAllowed($service)) {
            return back()->withErrors(['stop' => 'Itinerary tidak dapat diubah karena tanggal layanan sudah terlewat.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'visit_time' => ['nullable', 'date_format:H:i'],
            'google_maps_url' => ['nullable', 'url', 'max:500', 'regex:#^https?://(maps\.app\.goo\.gl|goo\.gl|www\.google\.com|maps\.google\.com)/#i'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $service->stops()->create([
            'name' => $data['name'],
            'visit_time' => $data['visit_time'] ?? null,
            'google_maps_url' => $data['google_maps_url'] ?? null,
            'address' => $data['address'] ?? null,
            'sort_order' => ((int) $service->stops()->max('sort_order')) + 1,
            'status' => 'Planned',
            'added_by_driver' => false,
        ]);

        return back()->with('success', 'Destinasi berhasil ditambahkan.');
    }

    public function updateStop(Request $request, TransportService $service, ItineraryStop $stop)
    {
        abort_unless((int) $stop->transport_service_id === (int) $service->id, 404);

        if (! $this->serviceChangesAllowed($service)) {
            return back()->withErrors(['stop' => 'Itinerary tidak dapat diubah karena tanggal layanan sudah terlewat.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'visit_time' => ['nullable', 'date_format:H:i'],
            'google_maps_url' => ['nullable', 'url', 'max:500', 'regex:#^https?://(maps\.app\.goo\.gl|goo\.gl|www\.google\.com|maps\.google\.com)/#i'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $stop->update($data);

        return back()->with('success', 'Destinasi berhasil diperbarui.');
    }

    public function destroyStop(TransportService $service, ItineraryStop $stop)
    {
        abort_unless((int) $stop->transport_service_id === (int) $service->id, 404);

        if (! $this->serviceChangesAllowed($service)) {
            return back()->withErrors(['stop' => 'Itinerary tidak dapat diubah karena tanggal layanan sudah terlewat.']);
        }

        $stop->delete();

        $service->stops()->get()->each(function (ItineraryStop $item, int $index) {
            if ((int) $item->sort_order !== $index + 1) {
                $item->update(['sort_order' => $index + 1]);
            }
        });

        return back()->with('success', 'Destinasi berhasil dihapus.');
    }


    public function drivers()
    {
        $drivers = Driver::withCount('services')->orderBy('name')->paginate(20);
        $driverStats = [
            'total' => Driver::count(),
            'active' => Driver::where('is_active', 1)->count(),
            'inactive' => Driver::where('is_active', 0)->count(),
            'with_license' => Driver::whereNotNull('license_number')->where('license_number', '<>', '')->count(),
        ];
        return view('drivers.index', compact('drivers', 'driverStats'));
    }
    public function storeDriver(Request $r)
    {
        $d = $r->validate(['name' => 'required|string|max:150', 'phone' => 'nullable|string|max:40', 'license_number' => 'nullable|string|max:80', 'notes' => 'nullable|string|max:1000']);
        Driver::create($d + ['is_active' => $r->boolean('is_active')]);
        return back()->with('success', 'Driver disimpan.');
    }
    public function editDriver(Driver $driver)
    {
        return view('drivers.edit', [
            'driver' => $driver,
            'serviceCount' => $driver->services()->count(),
        ]);
    }
    public function updateDriver(Request $request, Driver $driver)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:40',
            'license_number' => 'nullable|string|max:80',
            'notes' => 'nullable|string|max:1000',
        ]);

        $driver->update($data + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('drivers.index')->with('success', 'Data driver berhasil diperbarui.');
    }
    public function destroyDriver(Driver $driver)
    {
        if ($driver->services()->exists()) {
            return back()->withErrors(['driver' => 'Driver tidak dapat dihapus karena masih digunakan pada layanan.']);
        }

        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'Driver berhasil dihapus.');
    }
    public function vehicles(Request $request)
    {
        $vehicles = Vehicle::orderBy('name')->paginate(20)->withQueryString();
        $vehicleStats = [
            'total' => Vehicle::count(),
            'Available' => Vehicle::where('status', 'Available')->count(),
            'Assigned' => Vehicle::where('status', 'Assigned')->count(),
            'Maintenance' => Vehicle::where('status', 'Maintenance')->count(),
            'Inactive' => Vehicle::where('status', 'Inactive')->count(),
        ];

        $monthParam = (string) $request->query('month', '');
        if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $monthParam)) {
            [$year, $monthNumber] = array_map('intval', explode('-', $monthParam));
            $calendarMonth = now('Asia/Makassar')->setDate($year, $monthNumber, 1)->startOfDay();
        } else {
            $calendarMonth = now('Asia/Makassar')->startOfMonth();
        }

        $calendarStart = $calendarMonth->copy()->startOfMonth();
        $calendarEnd = $calendarMonth->copy()->endOfMonth();

        $calendarVehicles = Vehicle::orderBy('name')->get();
        $palette = ['#4263eb', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#6366f1', '#84cc16', '#06b6d4'];
        $vehicleColors = [];
        foreach ($calendarVehicles as $index => $vehicle) {
            $vehicleColors[$vehicle->id] = $palette[$index] ?? 'hsl(' . (int) (($index * 137.508) % 360) . ', 68%, 45%)';
        }

        $calendarServices = TransportService::with(['vehicle', 'serviceType', 'driver'])
            ->whereDate('service_date', '<=', $calendarEnd->toDateString())
            ->whereDate('service_end_date', '>=', $calendarStart->toDateString())
            ->whereNotIn('status', ['Cancelled', 'Expired'])
            ->orderBy('service_date')
            ->get();

        $calendarEvents = [];
        foreach ($calendarServices as $service) {
            $start = $service->service_date->copy()->startOfDay();
            if ($start->lt($calendarStart)) {
                $start = $calendarStart->copy();
            }

            $end = $service->service_end_date->copy()->startOfDay();
            if ($end->gt($calendarEnd)) {
                $end = $calendarEnd->copy();
            }

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $calendarEvents[$date->format('Y-m-d')][] = [
                    'vehicle_id' => $service->vehicle_id,
                    'vehicle' => $service->vehicle?->name ?? 'Kendaraan',
                    'plate' => $service->vehicle?->plate_number ?? '-',
                    'type' => $service->serviceType?->name ?? 'Layanan',
                    'driver' => $service->driver?->name ?? 'Belum ditugaskan',
                    'start' => DateFormatter::format($service->service_date, $service->start_time),
                    'end' => DateFormatter::format($service->service_end_date, $service->end_time),
                    'code' => $service->service_code,
                    'url' => route('services.show', $service),
                    'status' => $service->status,
                ];
            }
        }

        $vehicleCalendarCounts = [];
        foreach ($calendarEvents as $events) {
            foreach (array_unique(array_column($events, 'vehicle_id')) as $vehicleId) {
                $vehicleCalendarCounts[$vehicleId] = ($vehicleCalendarCounts[$vehicleId] ?? 0) + 1;
            }
        }

        return view('vehicles.index', compact(
            'vehicles',
            'vehicleStats',
            'calendarVehicles',
            'vehicleColors',
            'calendarEvents',
            'calendarMonth',
            'vehicleCalendarCounts'
        ));
    }
    public function storeVehicle(Request $r)
    {
        $d = $r->validate(['name' => 'required|string|max:150', 'plate_number' => 'required|string|max:30|unique:vehicles,plate_number', 'capacity' => 'required|integer|min:1|max:100', 'status' => ['required', Rule::in(['Available', 'Assigned', 'Maintenance', 'Inactive'])], 'notes' => 'nullable|string|max:1000']);
        Vehicle::create($d);
        return back()->with('success', 'Kendaraan disimpan.');
    }
    public function editVehicle(Vehicle $vehicle)
    {
        return view('vehicles.edit', [
            'vehicle' => $vehicle,
            'serviceCount' => $vehicle->services()->count(),
        ]);
    }
    public function updateVehicle(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'plate_number' => ['required', 'string', 'max:30', Rule::unique('vehicles', 'plate_number')->ignore($vehicle->id)],
            'capacity' => 'required|integer|min:1|max:100',
            'status' => ['required', Rule::in(['Available', 'Assigned', 'Maintenance', 'Inactive'])],
            'notes' => 'nullable|string|max:1000',
        ]);

        $vehicle->update($data);

        return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil diperbarui.');
    }
    public function types()
    {
        $types = ServiceType::orderBy('name')->get();
        $typeStats = [
            'total' => ServiceType::count(),
            'active' => ServiceType::where('is_active', 1)->count(),
            'inactive' => ServiceType::where('is_active', 0)->count(),
        ];
        return view('types.index', compact('types', 'typeStats'));
    }
    public function storeType(Request $r)
    {
        $d = $r->validate(['name' => 'required|string|max:120', 'description' => 'nullable|string|max:500']);
        ServiceType::create($d + ['is_active' => $r->boolean('is_active')]);
        return back()->with('success', 'Tipe layanan disimpan.');
    }
    public function editType(ServiceType $type)
    {
        return view('types.edit', [
            'type' => $type,
            'serviceCount' => TransportService::where('service_type_id', $type->id)->count(),
            'reservationCount' => Reservation::where('service_type_id', $type->id)->count(),
        ]);
    }
    public function updateType(Request $request, ServiceType $type)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $type->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('types.index')->with('success', 'Tipe layanan berhasil diperbarui.');
    }
    public function contacts()
    {
        $contacts = EmergencyContact::orderByDesc('is_default')->get();
        $contactStats = [
            'total' => EmergencyContact::count(),
            'active' => EmergencyContact::where('is_active', 1)->count(),
            'inactive' => EmergencyContact::where('is_active', 0)->count(),
            'default' => EmergencyContact::where('is_default', 1)->count(),
        ];
        return view('contacts.index', compact('contacts', 'contactStats'));
    }
    public function storeContact(Request $r)
    {
        $d = $r->validate(['name' => 'required|string|max:150', 'phone' => 'required|string|max:40', 'whatsapp' => 'nullable|string|max:40']);
        if ($r->boolean('is_default')) EmergencyContact::query()->update(['is_default' => false]);
        EmergencyContact::create($d + ['is_default' => $r->boolean('is_default'), 'is_active' => $r->boolean('is_active')]);
        return back()->with('success', 'Kontak disimpan.');
    }

    public function reservationUsers()
    {
        $reservationUsers = User::query()
            ->where('role', 'reservation')
            ->withCount('reservations')
            ->orderBy('name')
            ->paginate(20);

        $reservationUserStats = [
            'total' => User::where('role', 'reservation')->count(),
            'active' => User::where('role', 'reservation')->where('is_active', 1)->count(),
            'inactive' => User::where('role', 'reservation')->where('is_active', 0)->count(),
            'with_requests' => User::where('role', 'reservation')->has('reservations')->count(),
        ];

        return view('users.index', compact('reservationUsers', 'reservationUserStats'));
    }

    public function storeReservationUser(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'role' => 'reservation',
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'User reservation berhasil ditambahkan.');
    }

    public function editReservationUser(User $user)
    {
        abort_unless(strtolower((string) $user->role) === 'reservation', 404);

        return view('users.edit', ['reservationUser' => $user]);
    }

    public function updateReservationUser(Request $request, User $user)
    {
        abort_unless(strtolower((string) $user->role) === 'reservation', 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('reservation-users.index')
            ->with('success', 'Data user reservation berhasil diperbarui.');
    }

    private function validPublicSpk(string $token): ?Spk
    {
        $spk = Spk::where('token_hash', hash('sha256', $token))->first();

        if (!$spk || $spk->revoked_at) {
            return null;
        }

        $this->completePublicSpkIfDue($spk);

        return $spk;
    }

    private function completePublicSpkIfDue(Spk $spk): void
    {
        $service = $spk->service;

        // Mulai hari setelah tanggal akhir layanan, SPK selesai otomatis dan terkunci.
        if (
            $service
            && now('Asia/Makassar')->toDateString() > $service->service_end_date->format('Y-m-d')
            && !in_array($service->status, ['Cancelled', 'Completed'], true)
        ) {
            DB::transaction(function () use ($spk, $service) {
                $service->update(['status' => 'Completed']);
                SpkEvent::firstOrCreate(
                    ['spk_id' => $spk->id, 'event_type' => 'service_completed'],
                    [
                        'transport_service_id' => $service->id,
                        'actor_type' => 'system',
                        'created_at' => now('UTC'),
                        'metadata' => ['automatic' => true],
                    ]
                );
            });
        }
    }

    private function publicWritable(Spk $spk): bool
    {
        $service = $spk->service;

        return $service
            && !in_array($service->status, ['Expired', 'Cancelled', 'Completed'], true)
            && now('Asia/Makassar')->toDateString() >= $service->service_date->format('Y-m-d')
            && now('Asia/Makassar')->toDateString() <= $service->service_end_date->format('Y-m-d');
    }

    private function renderPublicSpk(Spk $spk, ?string $token = null, bool $numberUrl = false)
    {
        abort_if($spk->revoked_at, 404);

        $this->completePublicSpkIfDue($spk);
        $spk->load([
            'service.driver',
            'service.vehicle',
            'service.serviceType',
            'service.stops',
            'service.reservation.requester',
            'events.stop',
        ]);

        $canCheckIn = $this->publicWritable($spk);

        return response()
            ->view('public.spk', compact('spk', 'token', 'numberUrl', 'canCheckIn'))
            ->header('Referrer-Policy', 'no-referrer')
            ->header('Cache-Control', 'no-store, private');
    }

    public function publicSpk(string $token)
    {
        $spk = $this->validPublicSpk($token);
        abort_unless($spk, 404);

        return $this->renderPublicSpk($spk, $token, false);
    }

    public function publicSpkByNumber(Spk $spk, string $spkNumber)
    {
        abort_unless(!$spk->revoked_at && hash_equals($spk->spk_number, $spkNumber), 404);

        return $this->renderPublicSpk($spk, null, true);
    }

    private function storePublicCheckIn(Request $r, Spk $spk)
    {
        abort_unless($this->publicWritable($spk), 403, 'SPK sudah kedaluwarsa atau di luar tanggal layanan.');

        $data = $r->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy_meters' => 'nullable|numeric|gt:0|max:100000',
            'note' => 'nullable|string|max:1000',
            'itinerary_stop_id' => 'nullable|integer|exists:service_itinerary_stops,id',
            'idempotency_key' => 'required|uuid',
        ]);

        $duplicate = SpkEvent::where('spk_id', $spk->id)
            ->where('metadata->idempotency_key', $data['idempotency_key'])
            ->first();

        if ($duplicate) {
            return back()->with('success', 'Check-in ini sudah tersimpan.');
        }

        $stop = null;
        if (!empty($data['itinerary_stop_id'])) {
            $stop = ItineraryStop::where('transport_service_id', $spk->transport_service_id)
                ->findOrFail($data['itinerary_stop_id']);

            if ($stop->status === 'Cancelled') {
                return back()->withErrors(['itinerary_stop_id' => 'Destinasi ini sudah dibatalkan.']);
            }

            if ($stop->status === 'Checked In') {
                return back()->withErrors(['itinerary_stop_id' => 'Destinasi ini sudah check-in.']);
            }
        }

        DB::transaction(function () use ($spk, $data, $stop) {
            SpkEvent::create([
                'spk_id' => $spk->id,
                'transport_service_id' => $spk->transport_service_id,
                'itinerary_stop_id' => $stop?->id,
                'event_type' => 'check_in',
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'accuracy_meters' => $data['accuracy_meters'] ?? null,
                'note' => $data['note'] ?? null,
                'actor_type' => 'driver_public',
                'actor_id' => null,
                'created_at' => now('UTC'),
                'metadata' => ['idempotency_key' => $data['idempotency_key']],
            ]);

            if ($stop) {
                $stop->update(['status' => 'Checked In']);
            }

            if ($spk->service->status === 'Published') {
                $spk->service->update(['status' => 'In Progress']);
            }
        });

        return back()->with('success', 'Check-in berhasil disimpan.');
    }

    public function publicCheckIn(Request $r, string $token)
    {
        $spk = $this->validPublicSpk($token);
        abort_unless($spk, 404);

        return $this->storePublicCheckIn($r, $spk);
    }

    public function publicCheckInByNumber(Request $r, Spk $spk, string $spkNumber)
    {
        abort_unless(!$spk->revoked_at && hash_equals($spk->spk_number, $spkNumber), 404);
        $this->completePublicSpkIfDue($spk);

        return $this->storePublicCheckIn($r, $spk);
    }

    private function storePublicDestination(Request $r, Spk $spk)
    {
        abort_unless($this->publicWritable($spk), 403, 'SPK sudah tidak dapat diubah.');
        $data = $r->validate([
            'name' => 'required|string|max:150',
            'visit_time' => 'required|date_format:H:i',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy_meters' => 'nullable|numeric|gt:0|max:100000',
            'address' => 'nullable|string|max:500',
            'note' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($spk, $data) {
            $max = (int) ItineraryStop::where('transport_service_id', $spk->transport_service_id)->max('sort_order');
            $stop = ItineraryStop::create([
                'transport_service_id' => $spk->transport_service_id,
                'name' => $data['name'],
                'visit_time' => $data['visit_time'],
                'google_maps_url' => null,
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'address' => $data['address'] ?? null,
                'note' => $data['note'] ?? null,
                'sort_order' => $max + 1,
                'status' => 'Checked In',
                'added_by_driver' => true,
            ]);

            SpkEvent::create([
                'spk_id' => $spk->id,
                'transport_service_id' => $spk->transport_service_id,
                'itinerary_stop_id' => $stop->id,
                'event_type' => 'destination_added',
                'actor_type' => 'driver_public',
                'created_at' => now('UTC'),
                'metadata' => ['note' => $data['note'] ?? null],
            ]);

            SpkEvent::create([
                'spk_id' => $spk->id,
                'transport_service_id' => $spk->transport_service_id,
                'itinerary_stop_id' => $stop->id,
                'event_type' => 'check_in',
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'accuracy_meters' => $data['accuracy_meters'] ?? null,
                'note' => $data['note'] ?? null,
                'actor_type' => 'driver_public',
                'created_at' => now('UTC'),
                'metadata' => ['automatic_after_destination_added' => true],
            ]);

            if ($spk->service->status === 'Published') {
                $spk->service->update(['status' => 'In Progress']);
            }
        });

        return back()->with('success', 'Destinasi berhasil ditambahkan dan check-in otomatis telah dicatat.');
    }

    public function publicAddDestination(Request $r, string $token)
    {
        $spk = $this->validPublicSpk($token);
        abort_unless($spk, 404);

        return $this->storePublicDestination($r, $spk);
    }

    public function publicAddDestinationByNumber(Request $r, Spk $spk, string $spkNumber)
    {
        abort_unless(!$spk->revoked_at && hash_equals($spk->spk_number, $spkNumber), 404);
        $this->completePublicSpkIfDue($spk);

        return $this->storePublicDestination($r, $spk);
    }

    private function storePublicCancelDestination(Request $r, Spk $spk, ItineraryStop $stop)
    {
        abort_unless($this->publicWritable($spk), 403, 'SPK sudah tidak dapat diubah.');
        abort_unless((int) $stop->transport_service_id === (int) $spk->transport_service_id, 404);

        if ($stop->status === 'Checked In') {
            return back()->withErrors(['stop' => 'Destinasi sudah check-in dan tidak dapat dibatalkan.']);
        }

        $data = $r->validate(['reason' => 'nullable|string|max:500']);

        DB::transaction(function () use ($spk, $stop, $data) {
            $stop->update(['status' => 'Cancelled', 'cancelled_at' => now('UTC')]);

            SpkEvent::create([
                'spk_id' => $spk->id,
                'transport_service_id' => $spk->transport_service_id,
                'itinerary_stop_id' => $stop->id,
                'event_type' => 'destination_cancelled',
                'note' => $data['reason'] ?? null,
                'actor_type' => 'driver_public',
                'created_at' => now('UTC'),
                'metadata' => [],
            ]);
        });

        return back()->with('success', 'Kunjungan destinasi dibatalkan dan riwayat tetap disimpan.');
    }

    public function publicCancelDestination(Request $r, string $token, ItineraryStop $stop)
    {
        $spk = $this->validPublicSpk($token);
        abort_unless($spk, 404);

        return $this->storePublicCancelDestination($r, $spk, $stop);
    }

    public function publicCancelDestinationByNumber(Request $r, Spk $spk, string $spkNumber, ItineraryStop $stop)
    {
        abort_unless(!$spk->revoked_at && hash_equals($spk->spk_number, $spkNumber), 404);
        $this->completePublicSpkIfDue($spk);

        return $this->storePublicCancelDestination($r, $spk, $stop);
    }

}
