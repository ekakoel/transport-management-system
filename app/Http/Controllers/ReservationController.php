<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\EmergencyContact;
use App\Models\Reservation;
use App\Models\ServiceType;
use App\Models\Spk;
use App\Models\TransportService;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $reservations = Reservation::with('serviceType')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return view('reservations.index', compact('reservations'));
    }

    public function create()
    {
        return view('reservations.create', [
            'types' => ServiceType::where('is_active', 1)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_type_id' => ['required', 'exists:service_types,id'],
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:100'],
            'service_date' => ['required', 'date', 'after_or_equal:today'],
            'service_end_date' => ['required', 'date', 'after_or_equal:service_date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'itinerary_stops' => ['nullable', 'array', 'max:40'],
            'itinerary_stops.*.name' => ['required_with:itinerary_stops.*.visit_time', 'string', 'max:150'],
            'itinerary_stops.*.visit_time' => ['required_with:itinerary_stops.*.name', 'date_format:H:i'],
            'itinerary_stops.*.google_maps_url' => ['required_with:itinerary_stops.*.name', 'url', 'max:500', 'regex:/^https?:\\/\\/(maps\\.app\\.goo\\.gl|goo\\.gl|www\\.google\\.com|maps\\.google\\.com)\\//i'],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $itineraryStops = $data['itinerary_stops'] ?? [];
        unset($data['itinerary_stops']);
        $data['itinerary'] = json_encode(array_values(array_filter($itineraryStops, fn ($stop) => !empty($stop['name'])), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $reservation = Reservation::create($data + [
            'reservation_number' => 'RSV-' . now('Asia/Makassar')->format('ymd') . '-' . Str::upper(Str::random(6)),
            'user_id' => $request->user()->id,
            'status' => 'Pending',
        ]);

        return redirect()->route('reservations.show', $reservation)->with('success', 'Permintaan transportasi berhasil dikirim untuk ditinjau admin.');
    }

    public function show(Request $request, Reservation $reservation)
    {
        $isAdmin = in_array(strtolower((string) $request->user()->role), ['admin', 'owner', 'manager'], true);
        abort_unless($isAdmin || $reservation->user_id === $request->user()->id, 403);

        $reservation->load(['serviceType', 'requester', 'transportService', 'reviewer']);
        return view('reservations.show', [
            'reservation' => $reservation,
            'isAdmin' => $isAdmin,
            'drivers' => $isAdmin ? Driver::where('is_active', 1)->orderBy('name')->get() : collect(),
            'vehicles' => $isAdmin ? Vehicle::where('status', 'Available')->orderBy('name')->get() : collect(),
            'contacts' => $isAdmin ? EmergencyContact::where('is_active', 1)->orderByDesc('is_default')->get() : collect(),
        ]);
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        abort_unless($reservation->user_id === $request->user()->id, 403);
        abort_unless($reservation->status === 'Pending', 422, 'Hanya permintaan Pending yang dapat dibatalkan.');

        $reservation->update(['status' => 'Cancelled']);
        return redirect()->route('reservations.index')->with('success', 'Permintaan berhasil dibatalkan.');
    }

    public function adminIndex(Request $request)
    {
        $query = Reservation::with(['requester', 'serviceType'])->latest();
        if ($request->filled('status')) $query->where('status', $request->string('status')->toString());
        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $query->where(function ($q) use ($term) {
                $q->where('reservation_number', 'like', "%{$term}%")
                  ->orWhere('customer_name', 'like', "%{$term}%")
                  ->orWhereHas('requester', fn ($u) => $u->where('name', 'like', "%{$term}%"));
            });
        }
        $reservations = $query->paginate(20)->withQueryString();
        return view('reservations.admin-index', compact('reservations'));
    }

    public function approve(Request $request, Reservation $reservation)
    {
        abort_unless($reservation->status === 'Pending', 422, 'Permintaan ini sudah diproses.');

        $data = $request->validate([
            'driver_id' => ['required', 'exists:drivers,id'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'emergency_contact_id' => ['nullable', 'exists:emergency_contacts,id'],
            'admin_notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $driver = Driver::where('is_active', 1)->findOrFail($data['driver_id']);
        $vehicle = Vehicle::where('status', 'Available')->findOrFail($data['vehicle_id']);
        $start = $reservation->service_date->toDateString();
        $end = $reservation->service_end_date->toDateString();

        $conflict = TransportService::whereNotIn('status', ['Cancelled', 'Expired', 'Completed'])
            ->where(function ($q) use ($driver, $vehicle) {
                $q->where('driver_id', $driver->id)->orWhere('vehicle_id', $vehicle->id);
            })
            ->whereDate('service_date', '<=', $end)
            ->whereDate('service_end_date', '>=', $start)
            ->exists();

        if ($conflict) {
            return back()->withInput()->withErrors(['driver_id' => 'Driver atau kendaraan sudah memiliki layanan pada tanggal tersebut.']);
        }

        $rawToken = Str::random(64);
        $service = DB::transaction(function () use ($reservation, $data, $request, $rawToken, $start, $end) {
            $locked = Reservation::whereKey($reservation->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'Pending', 422, 'Permintaan ini sudah diproses.');

            $service = TransportService::create([
                'service_code' => 'TMS-' . now('Asia/Makassar')->format('ymd') . '-' . Str::upper(Str::random(5)),
                'customer_name' => $locked->customer_name,
                'customer_phone' => $locked->customer_phone,
                'guest_count' => $locked->guest_count,
                'service_type_id' => $locked->service_type_id,
                'service_date' => $start,
                'service_end_date' => $end,
                'start_time' => $locked->start_time,
                'end_time' => $locked->end_time,
                'pickup_location' => $locked->pickup_location,
                'notes' => trim(($locked->notes ?? '') . (($data['admin_notes'] ?? '') ? "\nCatatan admin: " . $data['admin_notes'] : '')),
                'driver_id' => $data['driver_id'],
                'vehicle_id' => $data['vehicle_id'],
                'status' => 'Published',
                'created_by' => $request->user()->id,
            ]);

            $itineraryStops = json_decode((string) $locked->itinerary, true);
            if (is_array($itineraryStops)) {
                foreach ($itineraryStops as $i => $stop) {
                    if (empty($stop['name'])) continue;
                    $service->stops()->create([
                        'name' => $stop['name'],
                        'visit_time' => $stop['visit_time'] ?? null,
                        'google_maps_url' => $stop['google_maps_url'] ?? null,
                        'address' => null,
                        'sort_order' => $i + 1,
                        'status' => 'Planned',
                        'added_by_driver' => false,
                    ]);
                }
            } else {
                // Backward compatibility for older requests saved as one destination per line.
                $lines = preg_split('/\r\n|\r|\n/', (string) $locked->itinerary);
                foreach ($lines as $i => $line) {
                    $line = trim($line);
                    if ($line !== '') {
                        $service->stops()->create([
                            'name' => Str::limit($line, 150, ''),
                            'address' => null,
                            'sort_order' => $i + 1,
                            'status' => 'Planned',
                            'added_by_driver' => false,
                        ]);
                    }
                }
            }

            $contact = !empty($data['emergency_contact_id'])
                ? EmergencyContact::where('is_active', 1)->find($data['emergency_contact_id'])
                : EmergencyContact::where('is_active', 1)->where('is_default', 1)->first();

            Spk::create([
                'transport_service_id' => $service->id,
                'spk_number' => 'SPK-' . now('Asia/Makassar')->format('Ymd') . '-' . str_pad((string) $service->id, 5, '0', STR_PAD_LEFT),
                'token_hash' => hash('sha256', $rawToken),
                'published_at' => now(),
                'emergency_name' => $contact?->name ?? 'Operasional Bali Kami Tour',
                'emergency_phone' => $contact?->phone,
                'emergency_whatsapp' => $contact?->whatsapp,
            ]);

            $locked->update([
                'status' => 'Approved',
                'admin_notes' => $data['admin_notes'] ?? null,
                'transport_service_id' => $service->id,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            session(['new_spk_url' => route('public.spk.show', ['token' => $rawToken])]);
            return $service;
        });

        return redirect()->route('services.show', $service)->with('success', 'Permintaan disetujui. Transport Service dan SPK berhasil dibuat.');
    }

    public function reject(Request $request, Reservation $reservation)
    {
        abort_unless($reservation->status === 'Pending', 422, 'Permintaan ini sudah diproses.');
        $data = $request->validate(['admin_notes' => ['required', 'string', 'max:3000']]);

        $reservation->update([
            'status' => 'Rejected',
            'admin_notes' => $data['admin_notes'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.reservations.index')->with('success', 'Permintaan ditolak dan alasan disimpan.');
    }
}
