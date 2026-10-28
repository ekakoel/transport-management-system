@extends('layouts.app')
@section('title', 'Buat Permintaan Transportasi')
@section('content')
<style>
.resv-form-wrap{max-width:900px;margin:auto;color:var(--tms-text)}.resv-form-head{margin-bottom:24px}.resv-form-head h1{font-family:Manrope,sans-serif;margin:0 0 8px}.resv-muted{color:var(--tms-muted)}.resv-form-card{background:#fff;border:1px solid var(--tms-border);border-radius:16px;padding:24px}.resv-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.resv-field{display:flex;flex-direction:column;gap:7px}.resv-field.full{grid-column:1/-1}.resv-field label{font-weight:700;font-size:13px}.resv-field input,.resv-field select,.resv-field textarea{width:100%;box-sizing:border-box;border:1px solid var(--tms-border);border-radius:9px;padding:12px;font:inherit;background:#fff;color:var(--tms-text)}.resv-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:24px}.resv-btn{display:inline-flex;align-items:center;justify-content:center;padding:11px 16px;border:0;border-radius:10px;background:var(--tms-navy);color:#fff;text-decoration:none;font-weight:700;cursor:pointer}.resv-btn.alt{background:#f2f6fa;color:var(--tms-text)}@media(max-width:650px){.resv-fields{grid-template-columns:1fr}.resv-field.full{grid-column:auto}.resv-form-card{padding:16px}}
</style>
<div class="resv-form-wrap"><div class="resv-form-head"><h1>Buat Permintaan Transportasi</h1><p class="resv-muted">Permintaan akan berstatus Pending sampai ditinjau admin. SPK baru dibuat setelah disetujui.</p></div>
<form method="POST" action="{{ route('reservations.store') }}" class="resv-form-card">@csrf
<div class="resv-fields">
<div class="resv-field"><label for="customer_name">Nama pelanggan / pemesan *</label><input id="customer_name" name="customer_name" value="{{ old('customer_name',auth()->user()->name) }}" maxlength="150" required></div>
<div class="resv-field"><label for="customer_phone">Telepon</label><input id="customer_phone" name="customer_phone" type="tel" value="{{ old('customer_phone') }}" maxlength="40"></div>
<div class="resv-field"><label for="guest_count">Jumlah tamu *</label><input id="guest_count" name="guest_count" type="number" min="1" max="100" value="{{ old('guest_count', 1) }}" required></div>
<div class="resv-field"><label for="service_type_id">Tipe layanan *</label><select id="service_type_id" name="service_type_id" required><option value="">Pilih tipe</option>@foreach($types as $type)<option value="{{ $type->id }}" @selected(old('service_type_id')==$type->id)>{{ $type->name }}</option>@endforeach</select></div>

<div class="resv-field"><label for="service_date">Tanggal mulai *</label><input id="service_date" type="date" name="service_date" min="{{ now('Asia/Makassar')->toDateString() }}" value="{{ old('service_date') }}" required></div>
<div class="resv-field"><label for="service_end_date">Tanggal selesai *</label><input id="service_end_date" type="date" name="service_end_date" min="{{ now('Asia/Makassar')->toDateString() }}" value="{{ old('service_end_date') }}" required></div>
<div class="resv-field"><label for="start_time">Jam mulai</label><input id="start_time" type="time" name="start_time" value="{{ old('start_time') }}"></div>
<div class="resv-field"><label for="end_time">Jam selesai</label><input id="end_time" type="time" name="end_time" value="{{ old('end_time') }}"></div>
<div class="resv-field full">
<label>Itinerary / daftar tujuan</label>
<p class="resv-muted">Setiap destinasi disimpan terpisah dengan waktu kunjungan, nama, dan short link Google Maps.</p>
<div id="reservation-stops">
<div class="resv-stop-row" style="border:1px solid var(--tms-border);border-radius:10px;padding:14px;margin-bottom:12px">
<div class="resv-fields">
<div class="resv-field"><label>Waktu kunjungan</label><input type="time" name="itinerary_stops[0][visit_time]" required></div>
<div class="resv-field"><label>Nama destinasi</label><input type="text" name="itinerary_stops[0][name]" maxlength="150" placeholder="Ubud Palace" required></div>
<div class="resv-field full"><label>Short link Google Maps</label><input type="url" name="itinerary_stops[0][google_maps_url]" maxlength="500" placeholder="https://maps.app.goo.gl/..." required></div>
</div></div></div>
<button type="button" class="resv-btn alt" id="addReservationStop">+ Tambah destinasi</button>
</div>
<div class="resv-field full"><label>Daftar tamu / wisatawan</label><p class="resv-muted">Opsional bila data belum tersedia. Pemohon Reservation tidak otomatis menjadi tamu.</p>
<div id="reservation-guests"><div class="resv-guest-row" style="border:1px solid var(--tms-border);border-radius:10px;padding:12px;margin-bottom:10px"><div class="resv-fields">
<div class="resv-field"><label>Nama</label><input name="guests[0][name]" maxlength="150"></div>
<div class="resv-field"><label>Jenis kelamin</label><select name="guests[0][gender]"><option value="">Pilih</option><option value="Male">Male</option><option value="Female">Female</option></select></div>
<div class="resv-field"><label>Kategori usia</label><select name="guests[0][age_category]"><option value="">Pilih</option><option value="Adult">Adult</option><option value="Children">Children</option></select></div>
<div class="resv-field"><label>Kontak (opsional)</label><input name="guests[0][phone]" type="tel" maxlength="40"></div>
</div></div></div><button type="button" class="resv-btn alt" id="addReservationGuest">+ Tambah tamu</button></div><div class="resv-field full"><label for="notes">Catatan / kebutuhan khusus</label><textarea id="notes" name="notes" rows="3">{{ old('notes') }}</textarea></div>
</div><div class="resv-actions"><a class="resv-btn alt" href="{{ route('reservations.index') }}">Batal</a><button class="resv-btn" type="submit">Kirim Permintaan</button></div></form></div>
<script>
(() => {
 const guestContainer = document.getElementById('reservation-guests');
 const addGuest = document.getElementById('addReservationGuest');
 let guestIndex = 1;
 addGuest?.addEventListener('click', () => {
   if (!guestContainer || guestContainer.querySelectorAll('.resv-guest-row').length >= 100) return;
   const i = guestIndex++;
   const row = document.createElement('div');
   row.className = 'resv-guest-row';
   row.style.cssText = 'border:1px solid var(--tms-border);border-radius:10px;padding:12px;margin-bottom:10px';
   row.innerHTML = '<div class="resv-fields"><div class="resv-field"><label>Nama</label><input name="guests['+i+'][name]" maxlength="150"></div><div class="resv-field"><label>Jenis kelamin</label><select name="guests['+i+'][gender]"><option value="">Pilih</option><option value="Male">Male</option><option value="Female">Female</option></select></div><div class="resv-field"><label>Kategori usia</label><select name="guests['+i+'][age_category]"><option value="">Pilih</option><option value="Adult">Adult</option><option value="Children">Children</option></select></div><div class="resv-field"><label>Kontak (opsional)</label><input name="guests['+i+'][phone]" maxlength="40"></div></div><button type="button" class="resv-btn alt remove-guest">Hapus tamu</button>';
   row.querySelector('.remove-guest').addEventListener('click', () => row.remove());
   guestContainer.appendChild(row);
 });
 const container = document.getElementById('reservation-stops');
 const add = document.getElementById('addReservationStop');
 if (!container || !add) return;
 let index = 1;
 add.addEventListener('click', () => {
   if (container.querySelectorAll('.resv-stop-row').length >= 40) return;
   const row = document.createElement('div');
   row.className = 'resv-stop-row';
   row.style.cssText = 'border:1px solid var(--tms-border);border-radius:10px;padding:14px;margin-bottom:12px';
   row.innerHTML = '<div class="resv-fields"><div class="resv-field"><label>Waktu kunjungan</label><input type="time" name="itinerary_stops['+index+'][visit_time]" required></div><div class="resv-field"><label>Nama destinasi</label><input type="text" name="itinerary_stops['+index+'][name]" maxlength="150" required></div><div class="resv-field full"><label>Short link Google Maps</label><input type="url" name="itinerary_stops['+index+'][google_maps_url]" maxlength="500" placeholder="https://maps.app.goo.gl/..." required></div></div><button type="button" class="resv-btn alt remove-stop">Hapus destinasi</button>';
   row.querySelector('.remove-stop').addEventListener('click', () => row.remove());
   container.appendChild(row);
   index++;
 });
})();
</script>
@endsection
