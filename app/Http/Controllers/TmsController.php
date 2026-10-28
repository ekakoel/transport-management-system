<?php
namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\EmergencyContact;
use App\Models\ItineraryStop;
use App\Models\ServiceType;
use App\Models\Spk;
use App\Models\SpkEvent;
use App\Models\TransportService;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TmsController extends Controller
{
    public function dashboard(){
        if (strtolower((string) auth()->user()->role) === 'reservation') return redirect()->route('reservations.index');
        abort_unless(in_array(strtolower((string) auth()->user()->role), ['admin', 'owner', 'manager'], true), 403);
        $today=now('Asia/Makassar')->toDateString();
        return view('dashboard',[
            'todayCount'=>TransportService::whereDate('service_date',$today)->count(),
            'activeCount'=>TransportService::whereIn('status',['Published','In Progress'])->count(),
            'expiredCount'=>TransportService::where('status','Expired')->count(),
            'driverCount'=>Driver::where('is_active',1)->count(),
            'services'=>TransportService::with(['driver','vehicle','serviceType','spk'])->orderByDesc('service_date')->limit(8)->get()
        ]);
    }

    public function services(Request $request){
        $q=TransportService::with(['driver','vehicle','serviceType','spk'])->orderByDesc('service_date');
        if($request->filled('date')) $q->whereDate('service_date',$request->input('date'));
        if($request->filled('status')) $q->where('status',$request->input('status'));
        if($request->filled('q')) $q->where(function($w)use($request){$term=$request->input('q');$w->where('service_code','like',"%$term%")->orWhere('customer_name','like',"%$term%");});
        $services = $q->paginate(15)->withQueryString();
        $serviceStatusCounts = TransportService::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $upcomingServiceCount = TransportService::whereDate('service_date', '>=', now('Asia/Makassar')->toDateString())
            ->whereNotIn('status', ['Cancelled', 'Expired', 'Completed'])
            ->count();

        return view('services.index', compact('services', 'serviceStatusCounts', 'upcomingServiceCount'));
    }

    public function createService(){
        return view('services.create',[
            'drivers'=>Driver::where('is_active',1)->orderBy('name')->get(),
            'vehicles'=>Vehicle::where('status','Available')->orderBy('name')->get(),
            'types'=>ServiceType::where('is_active',1)->orderBy('name')->get(),
            'contacts'=>EmergencyContact::where('is_active',1)->orderByDesc('is_default')->get(),\n            'reservationUsers'=>User::where('role','reservation')->where('is_active',1)->orderBy('name')->get()
        ]);
    }

    public function storeService(Request $request){
        $data=$request->validate([
            'reservation_user_id'=>['required', Rule::exists('users','id')->where(fn($q)=>$q->where('role','reservation')->where('is_active',1))],\n            'customer_name'=>'nullable|string|max:150','customer_phone'=>'nullable|string|max:40',\n            'guests'=>'nullable|array|max:100','guests.*.name'=>'required_with:guests.*.gender|string|max:150',\n            'guests.*.gender'=>['required_with:guests.*.name',Rule::in(['Male','Female'])],\n            'guests.*.age_category'=>['required_with:guests.*.name',Rule::in(['Adult','Children'])],\n            'guests.*.phone'=>'nullable|string|max:40',
            'service_type_id'=>'required|exists:service_types,id','service_date'=>'required|date',
            'service_end_date'=>'required|date|after_or_equal:service_date','start_time'=>'nullable|date_format:H:i',
            'end_time'=>'nullable|date_format:H:i','pickup_location'=>'required|string|max:255',
            'driver_id'=>'required|exists:drivers,id','vehicle_id'=>'required|exists:vehicles,id',
            'notes'=>'nullable|string|max:2000','emergency_contact_id'=>'nullable|exists:emergency_contacts,id',
            'stops'=>'nullable|array|max:40','stops.*.name'=>'required_with:stops.*.visit_time|string|max:150',
            'stops.*.visit_time'=>'required_with:stops.*.name|date_format:H:i',
            'stops.*.google_maps_url'=>['required_with:stops.*.name','url','max:500','regex:/^https?:\\/\\/(maps\\.app\\.goo\\.gl|goo\\.gl|www\\.google\\.com|maps\\.google\\.com)\\//i'],
            'stops.*.address'=>'nullable|string|max:500'
        ]);
        if($data['service_date'] < now('Asia/Makassar')->toDateString()) return back()->withInput()->withErrors(['service_date'=>'Tanggal layanan tidak boleh di masa lalu.']);
        $conflict=$this->hasScheduleConflict((int)$data['driver_id'],(int)$data['vehicle_id'],$data['service_date'],$data['service_end_date']);
        if($conflict) return back()->withInput()->withErrors(['driver_id'=>'Driver atau kendaraan sudah memiliki layanan yang tanggalnya bertumpang tindih.']);
        $rawToken=Str::random(64);
        $service=DB::transaction(function()use($data,$rawToken,$request){
            $service=TransportService::create([
                'service_code'=>'TMS-'.now('Asia/Makassar')->format('ymd').'-'.Str::upper(Str::random(5)),
                'customer_name'=>$data['customer_name']??null,'customer_phone'=>$data['customer_phone']??null,\n                'reservation_user_id'=>$data['reservation_user_id'],
                'guest_count'=>max(1,count(array_filter($data['guests']??[],fn($g)=>!empty($g['name'])))),\n                'service_type_id'=>$data['service_type_id'],'service_date'=>$data['service_date'],'service_end_date'=>$data['service_end_date'],
                'start_time'=>$data['start_time']??null,'end_time'=>$data['end_time']??null,'pickup_location'=>$data['pickup_location'],
                'notes'=>$data['notes']??null,'driver_id'=>$data['driver_id'],'vehicle_id'=>$data['vehicle_id'],'status'=>'Published','created_by'=>$request->user()->id
            ]);
            foreach(($data['guests']??[]) as $guest) if(!empty($guest['name'])) $service->guests()->create($guest);\n            foreach(($data['stops']??[]) as $i=>$stop) if(!empty($stop['name'])) $service->stops()->create(['name'=>$stop['name'],'visit_time'=>$stop['visit_time'],'address'=>$stop['address']??null,'google_maps_url'=>$stop['google_maps_url'],'sort_order'=>$i+1,'status'=>'Planned','added_by_driver'=>false]);
            $contact=isset($data['emergency_contact_id'])?EmergencyContact::find($data['emergency_contact_id']):EmergencyContact::where('is_active',1)->where('is_default',1)->first();
            $spk=Spk::create(['transport_service_id'=>$service->id,'spk_number'=>'SPK-'.now('Asia/Makassar')->format('Ymd').'-'.str_pad((string)$service->id,5,'0',STR_PAD_LEFT),'token_hash'=>hash('sha256',$rawToken),'published_at'=>now(),'emergency_name'=>$contact?->name??'Operasional Bali Kami Tour','emergency_phone'=>$contact?->phone,'emergency_whatsapp'=>$contact?->whatsapp]);
            session(['new_spk_url'=>route('public.spk.number.show', [$spk->id, $spk->spk_number])]);
            return $service;
        });
        return redirect()->route('services.show',$service)->with('success','Layanan dan SPK berhasil diterbitkan. Simpan tautan publik SPK yang ditampilkan satu kali.');
    }

    private function hasScheduleConflict(int $driverId,int $vehicleId,string $start,string $end): bool {
        return TransportService::whereNotIn('status',['Cancelled','Expired'])
            ->where(function($q)use($driverId,$vehicleId){$q->where('driver_id',$driverId)->orWhere('vehicle_id',$vehicleId);})
            ->whereDate('service_date','<=',$end)->whereDate('service_end_date','>=',$start)->exists();
    }

    public function showService(TransportService $service){
        $service->load(['driver','vehicle','serviceType','stops','spk.events.stop','reservationOwner','reservation','guests']);
        $newUrl=session('new_spk_url');
        return view('services.show',compact('service','newUrl'));
    }
    
    public function previewSpk(TransportService $service)
    {
        $service->load(['driver', 'vehicle', 'serviceType', 'stops', 'spk.events.stop', 'reservationOwner', 'reservation', 'guests']);

        abort_unless($service->spk, 404);

        $newUrl = session('new_spk_url');

        return view('services.spk-preview', [
            'service' => $service,
            'spk' => $service->spk,
            'newUrl' => $newUrl,
        ]);
    }


    public function drivers(){
        $drivers = Driver::orderBy('name')->paginate(20);
        $driverStats = [
            'total' => Driver::count(),
            'active' => Driver::where('is_active', 1)->count(),
            'inactive' => Driver::where('is_active', 0)->count(),
            'with_license' => Driver::whereNotNull('license_number')->where('license_number', '<>', '')->count(),
        ];
        return view('drivers.index', compact('drivers', 'driverStats'));
    }
    public function storeDriver(Request $r){$d=$r->validate(['name'=>'required|string|max:150','phone'=>'nullable|string|max:40','license_number'=>'nullable|string|max:80','notes'=>'nullable|string|max:1000']);Driver::create($d+['is_active'=>$r->boolean('is_active')]);return back()->with('success','Driver disimpan.');}
    public function vehicles(){
        $vehicles = Vehicle::orderBy('name')->paginate(20);
        $vehicleStats = [
            'total' => Vehicle::count(),
            'Available' => Vehicle::where('status', 'Available')->count(),
            'Assigned' => Vehicle::where('status', 'Assigned')->count(),
            'Maintenance' => Vehicle::where('status', 'Maintenance')->count(),
            'Inactive' => Vehicle::where('status', 'Inactive')->count(),
        ];
        return view('vehicles.index', compact('vehicles', 'vehicleStats'));
    }
    public function storeVehicle(Request $r){$d=$r->validate(['name'=>'required|string|max:150','plate_number'=>'required|string|max:30|unique:vehicles,plate_number','capacity'=>'required|integer|min:1|max:100','status'=>['required',Rule::in(['Available','Assigned','Maintenance','Inactive'])],'notes'=>'nullable|string|max:1000']);Vehicle::create($d);return back()->with('success','Kendaraan disimpan.');}
    public function types(){
        $types = ServiceType::orderBy('name')->get();
        $typeStats = [
            'total' => ServiceType::count(),
            'active' => ServiceType::where('is_active', 1)->count(),
            'inactive' => ServiceType::where('is_active', 0)->count(),
        ];
        return view('types.index', compact('types', 'typeStats'));
    }
    public function storeType(Request $r){$d=$r->validate(['name'=>'required|string|max:120','description'=>'nullable|string|max:500']);ServiceType::create($d+['is_active'=>$r->boolean('is_active')]);return back()->with('success','Tipe layanan disimpan.');}
    public function contacts(){
        $contacts = EmergencyContact::orderByDesc('is_default')->get();
        $contactStats = [
            'total' => EmergencyContact::count(),
            'active' => EmergencyContact::where('is_active', 1)->count(),
            'inactive' => EmergencyContact::where('is_active', 0)->count(),
            'default' => EmergencyContact::where('is_default', 1)->count(),
        ];
        return view('contacts.index', compact('contacts', 'contactStats'));
    }
    public function storeContact(Request $r){$d=$r->validate(['name'=>'required|string|max:150','phone'=>'required|string|max:40','whatsapp'=>'nullable|string|max:40']);if($r->boolean('is_default')) EmergencyContact::query()->update(['is_default'=>false]);EmergencyContact::create($d+['is_default'=>$r->boolean('is_default'),'is_active'=>$r->boolean('is_active')]);return back()->with('success','Kontak disimpan.');}

    private function validPublicSpk(string $token): ?Spk
    {
        $spk = Spk::where('token_hash', hash('sha256', $token))->first();

        if (!$spk || $spk->revoked_at) {
            return null;
        }

        $this->expirePublicSpkIfNeeded($spk);

        return $spk;
    }

    private function expirePublicSpkIfNeeded(Spk $spk): void
    {
        $service = $spk->service;

        // SPK tetap dapat dibaca setelah selesai; status berubah menjadi Expired
        // setelah tanggal akhir layanan terlewati. SPK yang dibatalkan tetap Cancelled.
        if (
            $service
            && now('Asia/Makassar')->toDateString() > $service->service_end_date->format('Y-m-d')
            && $service->status !== 'Cancelled'
            && $service->status !== 'Expired'
        ) {
            $service->update(['status' => 'Expired']);
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

        $this->expirePublicSpkIfNeeded($spk);
        $spk->load(['service.driver', 'service.vehicle', 'service.serviceType', 'service.stops', 'events.stop']);

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

        return back()->with('success', 'Check-in GPS berhasil disimpan.');
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
        $this->expirePublicSpkIfNeeded($spk);

        return $this->storePublicCheckIn($r, $spk);
    }

    private function storePublicDestination(Request $r, Spk $spk)
    {
        abort_unless($this->publicWritable($spk), 403, 'SPK sudah tidak dapat diubah.');
        $data = $r->validate([
            'name' => 'required|string|max:150',
            'visit_time' => 'required|date_format:H:i',
            'google_maps_url' => ['required', 'url', 'max:500', 'regex:/^https?:\\/\\/(maps\\.app\\.goo\\.gl|goo\\.gl|www\\.google\\.com|maps\\.google\\.com)\\//i'],
            'address' => 'nullable|string|max:500',
            'note' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($spk, $data) {
            $max = (int) ItineraryStop::where('transport_service_id', $spk->transport_service_id)->max('sort_order');
            $stop = ItineraryStop::create([
                'transport_service_id' => $spk->transport_service_id,
                'name' => $data['name'],
                'visit_time' => $data['visit_time'],
                'google_maps_url' => $data['google_maps_url'],
                'address' => $data['address'] ?? null,
                'note' => $data['note'] ?? null,
                'sort_order' => $max + 1,
                'status' => 'Planned',
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
        });

        return back()->with('success', 'Destinasi tambahan berhasil disimpan.');
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
        $this->expirePublicSpkIfNeeded($spk);

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
        $this->expirePublicSpkIfNeeded($spk);

        return $this->storePublicCancelDestination($r, $spk, $stop);
    }

    private function storePublicComplete(Spk $spk)
    {
        abort_unless($this->publicWritable($spk), 403, 'SPK sudah tidak dapat diubah.');

        DB::transaction(function () use ($spk) {
            $spk->service->update(['status' => 'Completed']);

            SpkEvent::create([
                'spk_id' => $spk->id,
                'transport_service_id' => $spk->transport_service_id,
                'event_type' => 'service_completed',
                'actor_type' => 'driver_public',
                'created_at' => now('UTC'),
                'metadata' => [],
            ]);
        });

        return back()->with('success', 'Layanan ditandai selesai.');
    }

    public function publicComplete(string $token)
    {
        $spk = $this->validPublicSpk($token);
        abort_unless($spk, 404);

        return $this->storePublicComplete($spk);
    }

    public function publicCompleteByNumber(Spk $spk, string $spkNumber)
    {
        abort_unless(!$spk->revoked_at && hash_equals($spk->spk_number, $spkNumber), 404);
        $this->expirePublicSpkIfNeeded($spk);

        return $this->storePublicComplete($spk);
    }
}
