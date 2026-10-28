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
            'contacts'=>EmergencyContact::where('is_active',1)->orderByDesc('is_default')->get()
        ]);
    }

    public function storeService(Request $request){
        $data=$request->validate([
            'customer_name'=>'required|string|max:150','customer_phone'=>'nullable|string|max:40',
            'service_type_id'=>'required|exists:service_types,id','service_date'=>'required|date',
            'service_end_date'=>'required|date|after_or_equal:service_date','start_time'=>'nullable|date_format:H:i',
            'end_time'=>'nullable|date_format:H:i','pickup_location'=>'required|string|max:255',
            'driver_id'=>'required|exists:drivers,id','vehicle_id'=>'required|exists:vehicles,id',
            'notes'=>'nullable|string|max:2000','emergency_contact_id'=>'nullable|exists:emergency_contacts,id',
            'stops'=>'nullable|array|max:40','stops.*.name'=>'required_with:stops|string|max:150',
            'stops.*.address'=>'nullable|string|max:500'
        ]);
        if($data['service_date'] < now('Asia/Makassar')->toDateString()) return back()->withInput()->withErrors(['service_date'=>'Tanggal layanan tidak boleh di masa lalu.']);
        $conflict=$this->hasScheduleConflict((int)$data['driver_id'],(int)$data['vehicle_id'],$data['service_date'],$data['service_end_date']);
        if($conflict) return back()->withInput()->withErrors(['driver_id'=>'Driver atau kendaraan sudah memiliki layanan yang tanggalnya bertumpang tindih.']);
        $rawToken=Str::random(64);
        $service=DB::transaction(function()use($data,$rawToken,$request){
            $service=TransportService::create([
                'service_code'=>'TMS-'.now('Asia/Makassar')->format('ymd').'-'.Str::upper(Str::random(5)),
                'customer_name'=>$data['customer_name'],'customer_phone'=>$data['customer_phone']??null,
                'service_type_id'=>$data['service_type_id'],'service_date'=>$data['service_date'],'service_end_date'=>$data['service_end_date'],
                'start_time'=>$data['start_time']??null,'end_time'=>$data['end_time']??null,'pickup_location'=>$data['pickup_location'],
                'notes'=>$data['notes']??null,'driver_id'=>$data['driver_id'],'vehicle_id'=>$data['vehicle_id'],'status'=>'Published','created_by'=>$request->user()->id
            ]);
            foreach(($data['stops']??[]) as $i=>$stop) if(!empty($stop['name'])) $service->stops()->create(['name'=>$stop['name'],'address'=>$stop['address']??null,'sort_order'=>$i+1,'status'=>'Planned','added_by_driver'=>false]);
            $contact=isset($data['emergency_contact_id'])?EmergencyContact::find($data['emergency_contact_id']):EmergencyContact::where('is_active',1)->where('is_default',1)->first();
            $spk=Spk::create(['transport_service_id'=>$service->id,'spk_number'=>'SPK-'.now('Asia/Makassar')->format('Ymd').'-'.str_pad((string)$service->id,5,'0',STR_PAD_LEFT),'token_hash'=>hash('sha256',$rawToken),'published_at'=>now(),'emergency_name'=>$contact?->name??'Operasional Bali Kami Tour','emergency_phone'=>$contact?->phone,'emergency_whatsapp'=>$contact?->whatsapp]);
            session(['new_spk_url'=>route('public.spk.show',['token'=>$rawToken])]);
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
        $service->load(['driver','vehicle','serviceType','stops','spk.events.stop']);
        $newUrl=session('new_spk_url');
        return view('services.show',compact('service','newUrl'));
    }
    
    public function previewSpk(TransportService $service)
    {
        $service->load(['driver', 'vehicle', 'serviceType', 'stops', 'spk.events.stop']);

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

    private function validPublicSpk(string $token): ?Spk {
        $hash=hash('sha256',$token);
        $spk=Spk::where('token_hash',$hash)->first();
        if(!$spk || $spk->revoked_at) return null;
        $service=$spk->service;
        if(!$service) return null;
        if(now('Asia/Makassar')->toDateString() > $service->service_end_date->format('Y-m-d') && !in_array($service->status,['Expired','Cancelled','Completed'])) {
            $service->update(['status'=>'Expired']);
        }
        return $spk;
    }
    private function publicWritable(Spk $spk): bool {
        $s=$spk->service;
        return $s && !in_array($s->status,['Expired','Cancelled','Completed']) && now('Asia/Makassar')->toDateString()>=$s->service_date->format('Y-m-d') && now('Asia/Makassar')->toDateString()<=$s->service_end_date->format('Y-m-d');
    }
    public function publicSpk(string $token){
        $spk=$this->validPublicSpk($token);
        abort_unless($spk,404);
        $spk->load(['service.driver','service.vehicle','service.serviceType','service.stops','events.stop']);
        return response()->view('public.spk',compact('spk','token'))->header('Referrer-Policy','no-referrer')->header('Cache-Control','no-store');
    }
    public function publicCheckIn(Request $r,string $token){
        $spk=$this->validPublicSpk($token);abort_unless($spk,404);
        abort_unless($this->publicWritable($spk),403,'SPK tidak dapat diubah di luar tanggal layanan atau statusnya sudah final.');
        $d=$r->validate(['latitude'=>'required|numeric|between:-90,90','longitude'=>'required|numeric|between:-180,180','accuracy_meters'=>'nullable|numeric|gt:0|max:100000','note'=>'nullable|string|max:1000','itinerary_stop_id'=>'nullable|integer|exists:service_itinerary_stops,id','idempotency_key'=>'required|uuid']);
        $duplicate=SpkEvent::where('spk_id',$spk->id)->where('metadata->idempotency_key',$d['idempotency_key'])->first();
        if($duplicate)return back()->with('success','Check-in ini sudah tersimpan.')->with('event_id',$duplicate->id);
        $stop=null;
        if(!empty($d['itinerary_stop_id'])){$stop=ItineraryStop::where('transport_service_id',$spk->transport_service_id)->findOrFail($d['itinerary_stop_id']);if($stop->status==='Cancelled')return back()->withErrors(['itinerary_stop_id'=>'Destinasi ini sudah dibatalkan.']);}
        DB::transaction(function()use($spk,$d,$stop){
            SpkEvent::create(['spk_id'=>$spk->id,'transport_service_id'=>$spk->transport_service_id,'itinerary_stop_id'=>$stop?->id,'event_type'=>'check_in','latitude'=>$d['latitude'],'longitude'=>$d['longitude'],'accuracy_meters'=>$d['accuracy_meters']??null,'note'=>$d['note']??null,'actor_type'=>'driver_public','actor_id'=>null,'created_at'=>now('UTC'),'metadata'=>['idempotency_key'=>$d['idempotency_key']]]);
            if($stop)$stop->update(['status'=>'Checked In']);
            if($spk->service->status==='Published')$spk->service->update(['status'=>'In Progress']);
        });
        return back()->with('success','Check-in berhasil disimpan.');
    }
    public function publicAddDestination(Request $r,string $token){
        $spk=$this->validPublicSpk($token);abort_unless($spk,404);abort_unless($this->publicWritable($spk),403,'SPK sudah tidak dapat diubah.');
        $d=$r->validate(['name'=>'required|string|max:150','address'=>'required|string|max:500','note'=>'nullable|string|max:1000']);
        DB::transaction(function()use($spk,$d){$max=(int)ItineraryStop::where('transport_service_id',$spk->transport_service_id)->max('sort_order');$stop=ItineraryStop::create(['transport_service_id'=>$spk->transport_service_id,'name'=>$d['name'],'address'=>$d['address'],'note'=>$d['note']??null,'sort_order'=>$max+1,'status'=>'Planned','added_by_driver'=>true]);SpkEvent::create(['spk_id'=>$spk->id,'transport_service_id'=>$spk->transport_service_id,'itinerary_stop_id'=>$stop->id,'event_type'=>'destination_added','actor_type'=>'driver_public','created_at'=>now('UTC'),'metadata'=>['note'=>$d['note']??null]]);});
        return back()->with('success','Destinasi tambahan berhasil disimpan.');
    }
    public function publicCancelDestination(Request $r,string $token,ItineraryStop $stop){
        $spk=$this->validPublicSpk($token);abort_unless($spk,404);abort_unless($this->publicWritable($spk),403,'SPK sudah tidak dapat diubah.');
        abort_unless((int)$stop->transport_service_id===(int)$spk->transport_service_id,404);
        if($stop->status==='Checked In')return back()->withErrors(['stop'=>'Destinasi sudah check-in dan tidak dapat dibatalkan sebagai belum dikunjungi.']);
        $d=$r->validate(['reason'=>'nullable|string|max:500']);
        DB::transaction(function()use($spk,$stop,$d){$stop->update(['status'=>'Cancelled','cancelled_at'=>now('UTC')]);SpkEvent::create(['spk_id'=>$spk->id,'transport_service_id'=>$spk->transport_service_id,'itinerary_stop_id'=>$stop->id,'event_type'=>'destination_cancelled','note'=>$d['reason']??null,'actor_type'=>'driver_public','created_at'=>now('UTC'),'metadata'=>[]]);});
        return back()->with('success','Kunjungan destinasi dibatalkan dan riwayat tetap disimpan.');
    }
    public function publicComplete(string $token){
        $spk=$this->validPublicSpk($token);abort_unless($spk,404);abort_unless($this->publicWritable($spk),403,'SPK sudah tidak dapat diubah.');
        DB::transaction(function()use($spk){$spk->service->update(['status'=>'Completed']);SpkEvent::create(['spk_id'=>$spk->id,'transport_service_id'=>$spk->transport_service_id,'event_type'=>'service_completed','actor_type'=>'driver_public','created_at'=>now('UTC'),'metadata'=>[]]);});
        return back()->with('success','Layanan ditandai selesai.');
    }
}

