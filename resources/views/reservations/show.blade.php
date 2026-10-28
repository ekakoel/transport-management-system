@extends('layouts.app')
@section('title', 'Detail Permintaan')
@section('content')
<style>
.resv-wrap{max-width:1000px;margin:auto;color:var(--tms-text)}.resv-head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:22px}.resv-head h1{font-family:Manrope,sans-serif;margin:0 0 8px}.resv-muted{color:var(--tms-muted)}.resv-card{background:#fff;border:1px solid var(--tms-border);border-radius:16px;padding:22px;margin-bottom:18px}.resv-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.resv-label{display:block;font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:var(--tms-muted);margin-bottom:5px}.resv-value{font-weight:600;overflow-wrap:anywhere}.resv-status{display:inline-block;border-radius:20px;padding:5px 10px;background:#eef2f7;font-size:12px;font-weight:700}.resv-status.Pending{background:#fff3d9;color:#8b5b00}.resv-status.Approved{background:#dff8ed;color:#08734b}.resv-status.Rejected,.resv-status.Cancelled{background:#ffe5e8;color:#a51d35}.resv-field{display:flex;flex-direction:column;gap:7px;margin-bottom:14px}.resv-field label{font-weight:700;font-size:13px}.resv-field input,.resv-field select,.resv-field textarea{width:100%;box-sizing:border-box;border:1px solid var(--tms-border);border-radius:9px;padding:11px;font:inherit;background:#fff}.resv-btn{display:inline-flex;align-items:center;justify-content:center;padding:11px 15px;border:0;border-radius:9px;background:var(--tms-navy);color:#fff;text-decoration:none;font-weight:700;cursor:pointer}.resv-btn.green{background:#087c59}.resv-btn.red{background:#a51d35}.resv-actions{display:flex;gap:10px;flex-wrap:wrap}.resv-section-title{margin:0 0 18px;font-family:Manrope,sans-serif}@media(max-width:650px){.resv-grid{grid-template-columns:1fr}.resv-card{padding:16px}.resv-head{flex-direction:column}}
</style>
<div class="resv-wrap"><div class="resv-head"><div><h1>{{ $reservation->reservation_number }}</h1><p class="resv-muted">Diajukan {{ $reservation->created_at?->format('d M Y H:i') }} · Pemohon: {{ $reservation->requester?->name }}</p></div><span class="resv-status {{ $reservation->status }}">{{ $reservation->status }}</span></div>
<section class="resv-card"><h2 class="resv-section-title">Informasi Permintaan</h2><div class="resv-grid">
<div><span class="resv-label">Pelanggan</span><div class="resv-value">{{ $reservation->customer_name }}</div></div><div><span class="resv-label">Telepon</span><div class="resv-value">{{ $reservation->customer_phone ?: '—' }}</div></div>
<div><span class="resv-label">Tipe layanan</span><div class="resv-value">{{ $reservation->serviceType?->name ?? '—' }}</div></div>
<div><span class="resv-label">Tanggal mulai</span><div class="resv-value">{{ $reservation->service_date->format('d M Y') }} {{ $reservation->start_time ? substr((string)$reservation->start_time,0,5) : '' }}</div></div><div><span class="resv-label">Tanggal selesai</span><div class="resv-value">{{ $reservation->service_end_date->format('d M Y') }} {{ $reservation->end_time ? substr((string)$reservation->end_time,0,5) : '' }}</div></div>
<div style="grid-column:1/-1"><span class="resv-label">Daftar Tamu ({{ $reservation->guests->count() }})</span>
@forelse($reservation->guests as $guest)
<div class="resv-value" style="border:1px solid var(--tms-border);border-radius:8px;padding:10px;margin:8px 0">{{ $guest->name }} · {{ $guest->gender }} · {{ $guest->age_category }} @if($guest->phone) · {{ $guest->phone }} @endif</div>
@empty <div class="resv-value">Belum ada data tamu.</div> @endforelse
</div>
<div style="grid-column:1/-1"><span class="resv-label">Itinerary</span>
@php($itineraryStops = json_decode((string) $reservation->itinerary, true))
@if(is_array($itineraryStops))
    @forelse($itineraryStops as $stop)
        <div class="resv-value" style="border:1px solid var(--tms-border);border-radius:8px;padding:10px;margin:8px 0">
            <strong>{{ $stop['visit_time'] ?? '—' }} · {{ $stop['name'] ?? 'Destinasi' }}</strong>
            @if(!empty($stop['google_maps_url']))<div><a href="{{ $stop['google_maps_url'] }}" target="_blank" rel="noopener noreferrer">Buka Google Maps</a></div>@endif
        </div>
    @empty — @endforelse
@else
    <div class="resv-value" style="white-space:pre-line">{{ $reservation->itinerary ?: '—' }}</div>
@endif
</div><div style="grid-column:1/-1"><span class="resv-label">Catatan pemohon</span><div class="resv-value" style="white-space:pre-line">{{ $reservation->notes ?: '—' }}</div></div>
@if($reservation->admin_notes)<div style="grid-column:1/-1"><span class="resv-label">Catatan admin</span><div class="resv-value" style="white-space:pre-line">{{ $reservation->admin_notes }}</div></div>@endif
@if($reservation->transportService)<div style="grid-column:1/-1"><span class="resv-label">Transport Service</span><a href="{{ route('services.show',$reservation->transportService) }}">{{ $reservation->transportService->service_code }} · Buka detail layanan/SPK</a></div>@endif
</div></section>
@if($isAdmin && $reservation->status==='Pending')
<section class="resv-card"><h2 class="resv-section-title">Persetujuan Admin</h2><p class="resv-muted">Setujui hanya setelah memastikan jadwal, driver, kendaraan, dan kontak darurat. SPK akan diterbitkan otomatis.</p>
<form method="POST" action="{{ route('admin.reservations.approve',$reservation) }}">@csrf
<div class="resv-grid"><div class="resv-field"><label for="driver_id">Driver *</label><select name="driver_id" id="driver_id" required><option value="">Pilih driver</option>@foreach($drivers as $driver)<option value="{{ $driver->id }}" @selected(old('driver_id')==$driver->id)>{{ $driver->name }}{{ $driver->phone ? ' · '.$driver->phone : '' }}</option>@endforeach</select></div>
<div class="resv-field"><label for="vehicle_id">Kendaraan *</label><select name="vehicle_id" id="vehicle_id" required><option value="">Pilih kendaraan</option>@foreach($vehicles as $vehicle)<option value="{{ $vehicle->id }}" @selected(old('vehicle_id')==$vehicle->id)>{{ $vehicle->name }} · {{ $vehicle->plate_number }} ({{ $vehicle->capacity }} seat)</option>@endforeach</select></div>
<div class="resv-field"><label for="emergency_contact_id">Kontak darurat</label><select name="emergency_contact_id" id="emergency_contact_id"><option value="">Gunakan kontak default</option>@foreach($contacts as $contact)<option value="{{ $contact->id }}" @selected(old('emergency_contact_id')==$contact->id)>{{ $contact->name }} · {{ $contact->phone }}</option>@endforeach</select></div>
<div class="resv-field"><label for="admin_notes">Catatan admin (opsional)</label><textarea name="admin_notes" id="admin_notes" rows="2">{{ old('admin_notes') }}</textarea></div></div><div class="resv-actions"><button class="resv-btn green" type="submit">Setujui &amp; Buat SPK</button></div></form>
<hr style="border:0;border-top:1px solid var(--tms-border);margin:24px 0"><form method="POST" action="{{ route('admin.reservations.reject',$reservation) }}">@csrf<div class="resv-field"><label for="reject_admin_notes">Alasan penolakan *</label><textarea id="reject_admin_notes" name="admin_notes" rows="3" required>{{ old('admin_notes') }}</textarea></div><button class="resv-btn red" type="submit">Tolak Permintaan</button></form>
</section>
@endif
@if(!$isAdmin && $reservation->status==='Pending')<section class="resv-card"><form method="POST" action="{{ route('reservations.cancel',$reservation) }}">@csrf<button class="resv-btn red" type="submit" onclick="return confirm('Batalkan permintaan ini?')">Batalkan Permintaan</button></form></section>@endif
<div class="resv-actions"><a class="resv-btn" href="{{ $isAdmin ? route('admin.reservations.index') : route('reservations.index') }}">← Kembali ke daftar</a></div>
</div>
@endsection
