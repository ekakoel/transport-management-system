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
use App\Support\ScheduleConflict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TmsController extends Controller
{
    public function dashboard()
    {
        $role = strtolower((string) auth()->user()->role);

        if ($role === 'reservation') {
            return $this->reservationDashboard();
        }

        abort_unless(in_array($role, ['admin', 'owner', 'manager'], true), 403);
        $today = now('Asia/Makassar')->toDateString();

        $todayReservations = Reservation::query()
            ->whereDate('service_date', '<=', $today)
            ->whereDate('service_end_date', '>=', $today)
            ->whereIn('status', ['Pending', 'Approved']);

        $pendingReservationReviewCount = Reservation::where('status', 'Pending')->count();
        $todayReservationCount = (clone $todayReservations)->count();
        $todayPendingReservationCount = (clone $todayReservations)->where('status', 'Pending')->count();

        $todayActiveServices = TransportService::query()
            ->whereDate('service_date', '<=', $today)
            ->whereDate('service_end_date', '>=', $today)
            ->whereIn('status', ['Published', 'In Progress'])
            ->get(['id', 'status', 'driver_id', 'vehicle_id', 'plate_number']);

        $todayActiveServiceCount = $todayActiveServices->count();
        $todayInProgressServiceCount = $todayActiveServices->where('status', 'In Progress')->count();
        $incompleteTodayAssignmentCount = $todayActiveServices
            ->filter(fn ($service) => $service->driver_id === null
                || $service->vehicle_id === null
                || blank($service->plate_number))
            ->count();
        $activeVehicleCount = $todayActiveServices->pluck('vehicle_id')->filter()->unique()->count();
        $activeDriverCount = $todayActiveServices->pluck('driver_id')->filter()->unique()->count();

        return view('dashboard', [
            'todayReservationCount' => $todayReservationCount,
            'todayPendingReservationCount' => $todayPendingReservationCount,
            'pendingReservationReviewCount' => $pendingReservationReviewCount,
            'todayActiveServiceCount' => $todayActiveServiceCount,
            'todayInProgressServiceCount' => $todayInProgressServiceCount,
            'incompleteTodayAssignmentCount' => $incompleteTodayAssignmentCount,
            'activeVehicleCount' => $activeVehicleCount,
            'activeDriverCount' => $activeDriverCount,
            'latestDriverCheckIns' => $this->latestDriverCheckIns(),
            'services' => TransportService::with(['driver', 'vehicle', 'serviceType', 'spk'])
                ->whereDate('service_end_date', '>=', $today)
                ->whereIn('status', ['Published', 'In Progress'])
                ->orderBy('service_date')
                ->orderBy('start_time')
                ->limit(8)
                ->get()
        ]);
    }

    public function liveDashboardCheckIns()
    {
        return response()->json([
            'checkIns' => $this->latestDriverCheckIns(),
        ]);
    }

    private function latestDriverCheckIns(): array
    {
        return SpkEvent::query()
            ->where('event_type', 'check_in')
            ->with(['stop', 'spk', 'service.driver', 'service.vehicle'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function (SpkEvent $event) {
                $service = $event->service;
                $checkedInAt = $event->created_at?->copy()->setTimezone('Asia/Makassar');

                return [
                    'service_code' => $service?->service_code ?? 'Layanan',
                    'service_url' => $service ? route('services.show', $service) : null,
                    'spk_number' => $event->spk?->spk_number ?? 'SPK',
                    'driver' => $service?->driver?->name ?? 'Driver tidak tersedia',
                    'vehicle' => $service?->vehicle?->name,
                    'plate_number' => $service?->plate_number,
                    'destination' => $event->stop?->name ?? 'Destinasi tidak tersedia',
                    'note' => $event->note,
                    'checked_in_at' => $checkedInAt?->format('d M Y H:i') . ' WITA',
                ];
            })
            ->all();
    }

    private function reservationDashboard()
    {
        $today = now('Asia/Makassar')->toDateString();
        $userId = auth()->id();

        $base = Reservation::query()->where('user_id', $userId);
        $upcoming = (clone $base)->whereDate('service_end_date', '>=', $today);

        $pendingReservationCount = (clone $upcoming)->where('status', 'Pending')->count();
        $approvedReservationCount = (clone $upcoming)->where('status', 'Approved')->count();
        $rejectedReservationCount = (clone $upcoming)->where('status', 'Rejected')->count();
        $cancelledReservationCount = (clone $upcoming)->where('status', 'Cancelled')->count();

        $todayReservationCount = (clone $base)
            ->whereDate('service_date', '<=', $today)
            ->whereDate('service_end_date', '>=', $today)
            ->whereIn('status', ['Pending', 'Approved'])
            ->count();

        $totalReservationCount = (clone $base)->count();

        $reservations = (clone $upcoming)
            ->with('serviceType')
            ->orderBy('service_date')
            ->limit(8)
            ->get();

        return view('dashboard-reservation', [
            'pendingReservationCount' => $pendingReservationCount,
            'approvedReservationCount' => $approvedReservationCount,
            'rejectedReservationCount' => $rejectedReservationCount,
            'cancelledReservationCount' => $cancelledReservationCount,
            'todayReservationCount' => $todayReservationCount,
            'totalReservationCount' => $totalReservationCount,
            'reservations' => $reservations,
        ]);
    }

    public function services(Request $request)
    {
        $today = now('Asia/Makassar')->toDateString();

        $q = TransportService::with(['driver', 'vehicle', 'serviceType', 'spk', 'reservation', 'stops']);
        if ($request->filled('date')) $q->whereDate('service_date', $request->input('date'));
        if ($request->filled('status')) $q->where('status', $request->input('status'));
        if ($request->filled('q')) $q->where(function ($w) use ($request) {
            $term = $request->input('q');
            $w->where('service_code', 'like', "%$term%")
                ->orWhere('customer_name', 'like', "%$term%")
                ->orWhereHas('reservation', fn ($r) => $r->where('order_number', 'like', "%$term%"));
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
            'defaultEmergencyContact' => EmergencyContact::activeDefault(),
            'reservationUsers' => User::where('role', 'reservation')->where('is_active', 1)->orderBy('name')->get()
        ]);
    }

    public function storeService(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')->where(fn($q) => $q->where('role', 'reservation')->where('is_active', 1))],
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'nullable|string|max:40',
            'order_number' => 'nullable|string|max:100',
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
            'plate_number' => 'required|string|max:30',
            'notes' => 'nullable|string|max:2000',
            'stops' => 'required|array|min:1|max:40',
            'stops.*.name' => 'required|string|max:150',
            'stops.*.visit_time' => 'required|date_format:H:i',
            'stops.*.google_maps_url' => ['required', 'url', 'max:500', 'regex:#^https?://(maps\.app\.goo\.gl|goo\.gl|www\.google\.com|maps\.google\.com)/#i'],
            'stops.*.address' => 'nullable|string|max:500'
        ]);
        $contact = EmergencyContact::activeDefault();
        if (! $contact) {
            return back()->withInput()->withErrors([
                'emergency_contact' => 'Tidak ada Kontak Darurat aktif yang ditandai sebagai Default. Atur kontak default sebelum membuat layanan dan SPK.',
            ]);
        }

        $stops = array_values($data['stops']);
        if ($data['service_date'] < now('Asia/Makassar')->toDateString()) return back()->withInput()->withErrors(['service_date' => 'Tanggal layanan tidak boleh di masa lalu.']);

        // Validasi bentrok memakai rentang jam itinerary, bukan seluruh tanggal.
        $startTime = collect($stops)->min('visit_time');
        $endTime = collect($stops)->max('visit_time');
        $driverConflict = ScheduleConflict::findDriverConflict((int) $data['driver_id'], $data['service_date'], $data['service_end_date'], $startTime, $endTime);
        $vehicleConflict = ScheduleConflict::findVehicleConflict((int) $data['vehicle_id'], $data['service_date'], $data['service_end_date'], $startTime, $endTime);
        $conflictErrors = [];
        if ($driverConflict) $conflictErrors['driver_id'] = 'Driver sudah memiliki jadwal pada ' . ScheduleConflict::describe($driverConflict) . ' (' . $driverConflict->service_code . ').';
        if ($vehicleConflict) $conflictErrors['vehicle_id'] = 'Kendaraan sudah memiliki jadwal pada ' . ScheduleConflict::describe($vehicleConflict) . ' (' . $vehicleConflict->service_code . ').';
        if ($conflictErrors) return back()->withInput()->withErrors($conflictErrors);

        $rawToken = Str::random(64);
        $service = DB::transaction(function () use ($data, $stops, $rawToken, $request, $contact, $startTime, $endTime) {
            $service = TransportService::create([
                'service_code' => 'TMS-' . now('Asia/Makassar')->format('ymd') . '-' . Str::upper(Str::random(5)),
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'guest_count' => max(1, count(array_filter($data['guests'] ?? [], fn($g) => !empty($g['name'])))),
                'service_type_id' => $data['service_type_id'],
                'service_date' => $data['service_date'],
                'service_end_date' => $data['service_end_date'],
                'plate_number' => $data['plate_number'],
                'start_time' => $startTime,
                'end_time' => $endTime,
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
                'order_number' => $data['order_number'] ?? null,
                'user_id' => $data['user_id'],
                'service_type_id' => $data['service_type_id'],
                'customer_name' => $data['customer_name'],
                'guest_count' => $service->guest_count,
                'service_date' => $data['service_date'],
                'service_end_date' => $data['service_end_date'],
                'itinerary' => $stops,
                'notes' => $data['notes'] ?? null,
                'status' => 'Approved',
                'transport_service_id' => $service->id,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now('UTC'),
            ]);
            $service->update(['reservation_id' => $reservation->id]);
            foreach (($data['guests'] ?? []) as $guest) if (!empty($guest['name'])) $reservation->guests()->create($guest);
            $spk = Spk::create(['transport_service_id' => $service->id, 'spk_number' => 'SPK-' . now('Asia/Makassar')->format('Ymd') . '-' . str_pad((string)$service->id, 5, '0', STR_PAD_LEFT), 'token_hash' => hash('sha256', $rawToken), 'published_at' => now(), 'emergency_name' => $contact->name, 'emergency_phone' => $contact->phone, 'emergency_whatsapp' => $contact->whatsapp]);
            session()->flash('new_spk_url', route('public.spk.number.show', [$spk->id, $spk->spk_number]));
            return $service;
        });
        return redirect()->route('services.show', $service)->with('success', 'Layanan dan SPK berhasil diterbitkan. Simpan tautan publik SPK yang ditampilkan satu kali.');
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
        $phone = preg_replace('/\D+/', '', (string) ($reservation?->requester?->phone ?: $service->customer_phone));
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
            'Order Number: ' . ($reservation?->order_number ?: $reservation?->reservation_number ?: $service->service_code),
            'Date: ' . \App\Support\DateFormatter::format($service->service_date),
            'Type: ' . ($service->serviceType?->name ?? 'Layanan Transportasi'), '',
            'Guest Name:', ...$guestLines, '',
            'Destination:', ...$destinationLines, '',
            'Driver: ' . ($service->driver?->name ?? '-'),
            'Hp: ' . ($service->driver?->phone ?? '-'),
            'Vehicle: ' . ($service->vehicle?->name ?? '-'),
            'Police Number: ' . ($service->plate_number ?? '-'), '',
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


    public function drivers(Request $request)
    {
        $query = Driver::withCount('services');

        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status')->toString() === 'active' ? 1 : 0);
        }

        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('license_number', 'like', "%{$term}%");
            });
        }

        $drivers = $query->orderBy('name')->paginate(20)->withQueryString();
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
    public function vehicles(Request $request)
    {
        $vehicles = Vehicle::orderBy('name')->paginate(20)->withQueryString();
        $companyVehicles = Vehicle::query()->where('ownership_type', 'Company');
        $vehicleStatusCounts = (clone $companyVehicles)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');
        $totalVehicles = (int) (clone $companyVehicles)->count();
        $maintenanceVehicles = (int) ($vehicleStatusCounts['Maintenance'] ?? 0);
        $inactiveVehicles = (int) ($vehicleStatusCounts['Inactive'] ?? 0);
        $today = now('Asia/Makassar')->toDateString();
        $assignedTodayCount = TransportService::query()
            ->whereNotNull('vehicle_id')
            ->whereDate('service_date', '<=', $today)
            ->whereDate('service_end_date', '>=', $today)
            ->whereNotIn('status', ['Cancelled', 'Expired', 'Completed'])
            ->whereHas('vehicle', function ($query) {
                $query->where('ownership_type', 'Company')
                    ->whereNotIn('status', ['Maintenance', 'Inactive']);
            })
            ->distinct()
            ->count('vehicle_id');
        $vehicleStats = [
            'total' => $totalVehicles,
            'Available' => max(0, $totalVehicles - $assignedTodayCount - $maintenanceVehicles - $inactiveVehicles),
            'Assigned' => $assignedTodayCount,
            'Maintenance' => $maintenanceVehicles,
            'Inactive' => $inactiveVehicles,
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
                    'plate' => $service->plate_number ?? '-',
                    'type' => $service->serviceType?->name ?? 'Layanan',
                    'driver' => $service->driver?->name ?? 'Belum ditugaskan',
                    'start' => DateFormatter::format($service->service_date, $service->start_time),
                    'end' => $service->end_time
                        ? $service->service_end_date->copy()
                            ->setTimeFromTimeString($service->end_time)
                            ->addHours(ScheduleConflict::PREP_BUFFER_HOURS)
                            ->format('d M Y (H:i)')
                        : DateFormatter::format($service->service_end_date),
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
        $d = $r->validate([
            'name' => 'required|string|max:150',
            'ownership_type' => ['required', Rule::in(['Company', 'Vendor'])],
            'capacity' => 'required|integer|min:1|max:100',
            'status' => ['required', Rule::in(['Available', 'Assigned', 'Maintenance', 'Inactive'])],
            'notes' => 'nullable|string|max:1000',
        ]);
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
            'ownership_type' => ['required', Rule::in(['Company', 'Vendor'])],
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
        $data = $r->validate([
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:40',
            'whatsapp' => 'nullable|string|max:40',
        ]);

        DB::transaction(function () use ($r, $data) {
            if ($r->boolean('is_default')) {
                EmergencyContact::query()->update(['is_default' => false]);
            }

            EmergencyContact::create($data + [
                'is_default' => $r->boolean('is_default'),
                'is_active' => $r->boolean('is_active'),
            ]);
        });

        return back()->with('success', 'Kontak disimpan.');
    }
    public function updateContact(Request $request, EmergencyContact $contact)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:40',
            'whatsapp' => 'nullable|string|max:40',
        ]);

        DB::transaction(function () use ($request, $contact, $data) {
            if ($request->boolean('is_default')) {
                EmergencyContact::query()
                    ->where('id', '!=', $contact->id)
                    ->update(['is_default' => false]);
            }

            $contact->update($data + [
                'is_default' => $request->boolean('is_default'),
                'is_active' => $request->boolean('is_active'),
            ]);
        });

        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil diperbarui.');
    }
    public function destroyContact(EmergencyContact $contact)
    {
        $wasDefault = $contact->is_default;
        $contact->delete();

        $message = $wasDefault
            ? 'Kontak berhasil dihapus. Pilih kontak Default yang baru agar dapat digunakan pada layanan dan SPK.'
            : 'Kontak berhasil dihapus.';

        return redirect()->route('contacts.index')->with('success', $message);
    }

    public function users()
    {
        $users = User::query()
            ->withCount('reservations')
            ->orderBy('name')
            ->paginate(20);

        $userStats = [
            'total' => User::count(),
            'active' => User::where('is_active', 1)->count(),
            'inactive' => User::where('is_active', 0)->count(),
            'staff' => User::whereIn('role', ['admin', 'owner', 'manager'])->count(),
            'reservation' => User::where('role', 'reservation')->count(),
        ];

        $roles = $this->assignableRoles();
        $editingUser = old('user_id') ? User::find(old('user_id')) : null;

        return view('users.index', compact('users', 'userStats', 'roles', 'editingUser'));
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'role' => ['required', Rule::in(array_keys($this->assignableRoles()))],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'role' => ['required', Rule::in(array_keys($this->assignableRoles()))],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->user()->is($user) && ($data['role'] !== strtolower((string) $user->role) || ! $data['is_active'])) {
            return back()->withErrors(['user' => 'Anda tidak dapat mengubah role atau menonaktifkan akun sendiri.']);
        }

        if (
            strtolower((string) $user->role) === 'admin'
            && $data['role'] !== 'admin'
            && User::where('role', 'admin')->where('is_active', 1)->where('id', '!=', $user->id)->doesntExist()
        ) {
            return back()->withErrors(['role' => 'Minimal harus ada satu admin aktif di sistem.']);
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroyUser(Request $request, User $user)
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['user' => 'Anda tidak dapat menghapus akun sendiri.']);
        }

        if (
            strtolower((string) $user->role) === 'admin'
            && User::where('role', 'admin')->where('is_active', 1)->where('id', '!=', $user->id)->doesntExist()
        ) {
            return back()->withErrors(['user' => 'Minimal harus ada satu admin aktif di sistem.']);
        }

        if ($user->reservations()->exists()) {
            return back()->withErrors(['user' => 'User ini memiliki data reservasi. Nonaktifkan akun saja agar riwayat tetap tersimpan.']);
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

    private function assignableRoles(): array
    {
        return [
            'admin' => 'Admin',
            'owner' => 'Owner',
            'manager' => 'Manager',
            'reservation' => 'Reservation',
        ];
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
            'note' => 'required|string|max:1000',
                    'destination_name' => 'nullable|string|max:150',
            'itinerary_stop_id' => 'nullable|integer|exists:service_itinerary_stops,id',
            'idempotency_key' => 'required|uuid',
        ]);

                // Validate destination_name is provided when itinerary_stop_id is not given
                if (empty($data['itinerary_stop_id']) && empty($data['destination_name'])) {
                    return back()->withErrors(['destination_name' => 'Nama destinasi wajib diisi saat menambah destinasi baru.']);
                }

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
            $destinationStop = $stop;

            if (! $destinationStop && filled($data['destination_name'] ?? null)) {
                $maxSortOrder = (int) ItineraryStop::where('transport_service_id', $spk->transport_service_id)
                    ->max('sort_order');
                $destinationStop = ItineraryStop::create([
                    'transport_service_id' => $spk->transport_service_id,
                    'name' => $data['destination_name'],
                    'visit_time' => now('Asia/Makassar')->format('H:i:s'),
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'address' => null,
                    'note' => $data['note'] ?? null,
                    'sort_order' => $maxSortOrder + 1,
                    'status' => 'Checked In',
                    'added_by_driver' => true,
                ]);

                SpkEvent::create([
                    'spk_id' => $spk->id,
                    'transport_service_id' => $spk->transport_service_id,
                    'itinerary_stop_id' => $destinationStop->id,
                    'event_type' => 'destination_added',
                    'actor_type' => 'driver_public',
                    'actor_id' => null,
                    'created_at' => now('UTC'),
                    'metadata' => [
                        'idempotency_key' => $data['idempotency_key'],
                    ],
                ]);
            }

            SpkEvent::create([
                'spk_id' => $spk->id,
                'transport_service_id' => $spk->transport_service_id,
                'itinerary_stop_id' => $destinationStop?->id,
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

        return back()->with(
            'success',
            filled($data['destination_name'] ?? null)
                ? 'Destinasi berhasil ditambahkan dan laporan aktivitas tersimpan.'
                : 'Check-in berhasil disimpan.'
        );
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
