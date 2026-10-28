<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Perintah Kerja (SPK) {{ $spk->spk_number }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }
        h2{
            text-transform: uppercase;
            font-family: fangsong;
            margin-bottom: 0px;
        }
        h4{
            margin: 18px 0 4px 0;
            text-decoration: none;
        }
        .spk-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .section-title {
            margin-top: 20px;
            font-weight: bold;
        }
        .destination-container{
            border: 1px solid grey;
            padding: 18px;
            border-radius: 8px;
            margin-bottom: 27px;
            position: relative;
        }
        .number{
            position: absolute;
            top: 0px;
            left: 19px;
            font-size: 3rem;
            font-weight: 700;
            color: grey;
        }
        .destination {
            display: flex;
            align-items: flex-start;
        }
        .qrcode-container {
            width: fit-content;
            padding-right: 18px;
            justify-items: center;
        }
        .btn-modal-dismis{
            position: absolute;
            height: 30px;
            padding: 0 !important;
            border-radius: 50px;
            top: 13px;
            right: 14px;
            width: 30px;
            background-color: white;
            color: black;
            line-height: 1;
        }
        .qrcode {
            position: relative;
            width: fit-content;
            margin-bottom: 18px;
            border: 1px solid grey;
            padding: 18px 21px 13px 18px;
            border-radius: 9px;
        }
        .qrcode .overlay {
            border-radius: 8px;
            width: 120px;
            height: 120px;
            align-content: center;
        }
        .qrcode .overlay p{
            font-size: 11px;
            color: #555;
        }
        .qrcode .overlay a{
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translate(-50%, 0px);
            width: max-content;
        }
        .caption{
            width: 100%;
        }
        .caption i{
            font-size: 0.7rem;
            color: grey;
        }
        .caption p{
            font-size: 12px;
            color: rgb(46, 46, 46);
        }
        .caption .btn-checkin{
            width: 162px;
            height: 57px;
            margin: 18px 0;
            background-color: #1010ff;
            color: white;
            border-radius: 50px;
            border: 2px solid #000077;
        }

        .description{
            width: 100%;
        }
        .description-item {
            display: flex;
            align-items: center;
            margin-bottom: 6px; /* jarak antar item */
        }
        .description-item title{
            align-items: center;
        }

        .description-item .icon {
            width: 28px;         /* ukuran konsisten */
            height: 28px;        /* tinggi sama */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;     /* ukuran emoji/icon */
            margin-right: 8px;   /* jarak dengan teks */
            background: #f0f0f0; /* opsional, buat background kotak */
            border-radius: 50%;  /* opsional, bulat */
        }

        .description-item .desc {
            flex: 1;
            font-size: 14px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 10px 0;
        }
        th{
            width: 30%;
        }
        table {
            overflow: hidden;
            border: 1px solid grey;
        }
        th, td {
            border: 1px solid grey;
            padding: 6px;
            text-align: left;
            align-content: start;
        }
        .note {
            margin-top: 25px;
            font-size: 13px;
            border-top: 1px dashed #555;
            padding-top: 10px;
        }
        p{
            padding: 0 !important;
            margin: 0 !important;
        }
        .modal{
            background-color: #00000063;
            backdrop-filter: blur(2px);
        }
        .modal-header{
            background-color: #003481;
            color: white;
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-Pending{
            font-size: 2rem;
            color: grey;
        }
        .status-Visited{
            font-size: 2rem;
            color: green;
        }
        .btn{
            padding: 4px 18px;
        }
        .page-brake-avoid{
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .body-padding{
            padding: 27px 18%;
        }
        label span{
            color: red;
        }

        @media (max-width: 1024px) {
            .body-padding {
                padding: 18px 10%;
            }
        }

        @media (max-width: 768px) {
            .body-padding {
                padding: 18px 5%;
            }
        }

        @media (max-width: 480px) {
            .body-padding {
                padding: 44px 27px;
            }

            .destination {
                flex-direction: column !important;
            }
            .qrcode-container {
                width: 100%;
                padding-right: 0 !important;
                justify-items: center;
            }
            .caption .btn-checkin {
                width: 100%;
                height: 57px;
                margin: 18px 0;
                background-color: #1010ff;
                color: white;
                border-radius: 50px;
                border: 2px solid #000077;
            }
        }

        @media print {
            .body-padding {
                padding: 0 !important;
            }
        }

    
        .public-expired-banner { border:1px solid #b02a37; background:#f8d7da; color:#842029; padding:12px 16px; border-radius:6px; margin-bottom:18px; }
        .public-status { display:inline-block; padding:4px 10px; border:1px solid #777; border-radius:20px; font-weight:700; font-size:12px; text-transform:uppercase; }
        .public-status-active { color:#146c43; border-color:#198754; background:#d1e7dd; }
        .public-status-expired,.public-status-cancelled { color:#842029; border-color:#b02a37; background:#f8d7da; }
        .public-status-completed { color:#084298; border-color:#0d6efd; background:#cfe2ff; }
        .public-topline { display:flex; justify-content:space-between; gap:12px; align-items:center; flex-wrap:wrap; }
        .public-table-title { display:flex; justify-content:space-between; align-items:center; gap:12px; }
        .public-destination-title { display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap; }
        .public-action { display:inline-block; border:2px solid #000077; background:#1010ff; color:#fff; border-radius:50px; padding:10px 18px; text-decoration:none; cursor:pointer; margin:8px 0; }
        .public-action:disabled { opacity:.55; cursor:not-allowed; }
        .public-action-secondary { background:#fff; border-color:#777; color:#222; }
        .public-action-danger { background:#b02a37; border-color:#842029; }
        .public-field { margin:10px 0; }
        .public-field label { display:block; font-weight:700; margin-bottom:5px; }
        .public-field input,.public-field textarea { width:100%; border:1px solid #aaa; border-radius:4px; padding:8px; }
        .public-gps-message { border:1px solid #aaa; padding:10px; border-radius:5px; margin:12px 0; }
        .public-gps-success { background:#d1e7dd; border-color:#198754; color:#146c43; }
        .public-gps-error { background:#f8d7da; border-color:#b02a37; color:#842029; }
        .public-readonly { background:#f5f5f5; border:1px solid #bbb; padding:14px; margin:18px 0; border-radius:6px; }
        .public-destination-state { font-size:12px; border:1px solid #888; border-radius:20px; padding:3px 9px; white-space:nowrap; }
        .public-destination-state-checked { color:#146c43; border-color:#198754; background:#d1e7dd; }
        .public-destination-state-cancelled { color:#842029; border-color:#b02a37; background:#f8d7da; }
        .public-small { font-size:12px; color:#555; }
        .public-form-row { display:flex; gap:12px; flex-wrap:wrap; }
        .public-form-row > div { flex:1; min-width:220px; }
        .public-footer { border-top:1px dashed #555; margin-top:25px; padding-top:10px; text-align:center; font-size:11px; color:#666; }
        @media (max-width:480px) { .public-form-row { display:block; } .public-action { max-width:100%; } }

    </style>
</head>
<body class="body-padding">
@php
    $service = $spk->service;
    $expired = $service->status === 'Expired';
    $statusClass = $expired || $service->status === 'Cancelled' ? 'public-status-expired' : ($service->status === 'Completed' ? 'public-status-completed' : 'public-status-active');
    $showNumberUrl = $numberUrl ?? false;
    $checkinUrl = $showNumberUrl ? route('public.spk.number.checkin', [$spk->id, $spk->spk_number]) : route('public.spk.checkin', $token);
    $addDestinationUrl = $showNumberUrl ? route('public.spk.number.destinations.add', [$spk->id, $spk->spk_number]) : route('public.spk.destinations.add', $token);
    $completeUrl = $showNumberUrl ? route('public.spk.number.complete', [$spk->id, $spk->spk_number]) : route('public.spk.complete', $token);
@endphp

@if($expired)
    <div class="status-spk"><span class="public-status public-status-expired">SPK EXPIRED</span></div>
    <div class="public-expired-banner"><strong>Informasi SPK Kedaluwarsa</strong><ul class="mb-0 mt-2"><li>SPK ini telah melewati tanggal layanan.</li><li>Check-in dan perubahan data sudah ditutup.</li><li>Data tetap tersedia untuk arsip dan referensi laporan perjalanan.</li></ul></div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
@endif
@foreach($errors->all() as $error)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ $error }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
@endforeach

<div class="spk-header">
    <h2>Surat Perintah Kerja (SPK)</h2>
    <p><strong>Nomor:</strong> {{ $spk->spk_number }}</p>
    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($service->service_date)->locale('id')->translatedFormat('l, d M Y') }}</p>
    <p class="mt-2"><span class="public-status {{ $statusClass }}">{{ $service->status ?: 'Published' }}</span></p>
    <hr>
</div>

<h4 class="section-title">Pesanan (Order)</h4>
<table>
    <tr><th>Nomor Pesanan (Order)</th><td>{{ $service->service_code }}</td></tr>
    <tr><th>Nama Pelanggan / Pemesan</th><td>{{ $service->customer_name ?: '—' }}</td></tr>
    <tr><th>Telepon Pelanggan</th><td>{{ $service->customer_phone ?: '—' }}</td></tr>
    <tr><th>Jenis Pesanan (Type)</th><td>{{ $service->serviceType?->name ?? 'Transport Service' }}</td></tr>
    <tr><th>Tanggal Layanan</th><td>{{ \Carbon\Carbon::parse($service->service_date)->locale('id')->translatedFormat('l, d M Y') }}@if($service->service_end_date && $service->service_end_date->format('Y-m-d') !== $service->service_date->format('Y-m-d')) – {{ \Carbon\Carbon::parse($service->service_end_date)->locale('id')->translatedFormat('l, d M Y') }}@endif</td></tr>
    <tr><th>Jumlah Tamu</th><td>{{ $service->guest_count ?? 1 }} orang</td></tr>
    <tr><th>Lokasi Penjemputan</th><td>{{ $service->pickup_location ?: '—' }}</td></tr>
    <tr><th>Waktu Penjemputan</th><td>{{ $service->start_time ? substr((string) $service->start_time, 0, 5).' WITA' : '—' }}</td></tr>
    @if($service->notes)<tr><th>Catatan</th><td>{{ $service->notes }}</td></tr>@endif
</table>

<div class="row">
    <div class="col-md-6">
        <h4 class="section-title">Data Transport</h4>
        <table>
            <tr><th>Kendaraan</th><td>{{ $service->vehicle?->name ?? '—' }}</td></tr>
            <tr><th>Nomor Polisi</th><td>{{ $service->vehicle?->plate_number ?? '—' }}</td></tr>
            <tr><th>Kapasitas</th><td>{{ $service->vehicle?->capacity ?? '—' }}</td></tr>
        </table>
    </div>
    <div class="col-md-6">
        <h4 class="section-title">Data Driver</h4>
        <table>
            <tr><th>Nama Driver</th><td>{{ $service->driver?->name ?? '—' }}</td></tr>
            <tr><th>No. Telepon</th><td>{{ $service->driver?->phone ?? '—' }}</td></tr>
        </table>
    </div>
</div>

<h4 class="section-title">Kontak Darurat</h4>
<table>
    <tr><th>Nama Kontak</th><td>{{ $spk->emergency_name ?: 'Operasional Bali Kami Tour' }}</td></tr>
    <tr><th>Telepon</th><td>{{ $spk->emergency_phone ?: '—' }}</td></tr>
    <tr><th>WhatsApp</th><td>{{ $spk->emergency_whatsapp ?: '—' }}</td></tr>
</table>

<div class="page-brake-avoid">
    <h4 class="section-title">Destinasi / Tujuan</h4>
    @if($service->stops->isEmpty())
        <p class="text-muted">Belum ada destinasi yang tercatat.</p>
    @else
    @foreach($service->stops as $stop)
        <div class="destination-container page-brake-avoid">
            <div class="destination">
                <div class="description">
                    <div class="public-destination-title">
                        <h5><strong>{{ $stop->name }}</strong></h5>
                        <span class="public-destination-state {{ $stop->status === 'Checked In' ? 'public-destination-state-checked' : ($stop->status === 'Cancelled' ? 'public-destination-state-cancelled' : '') }}">{{ $stop->status }}</span>
                    </div>
                    <div class="description-item"><div class="icon">📍</div><div class="desc">Alamat / lokasi: {{ $stop->address ?: 'Belum dicatat' }}</div></div>
                    <div class="description-item"><div class="icon">🕒</div><div class="desc">Urutan destinasi: {{ $stop->sort_order }}</div></div>
                    @if($stop->note)<div class="description-item"><div class="icon">📋</div><div class="desc"><i>Catatan: {{ $stop->note }}</i></div></div>@endif
                    @if($stop->added_by_driver)<p class="public-small">Destinasi tambahan dari driver.</p>@endif
                    @if($stop->status === 'Checked In')
                        <p class="caption"><strong>✓ Check-in tercatat</strong></p>
                        @php
                            $stopEvent = $spk->events->firstWhere('itinerary_stop_id', $stop->id);
                        @endphp
                        @if($stopEvent && $stopEvent->latitude !== null && $stopEvent->longitude !== null)
                            <div class="description-item"><div class="icon">🗺️</div><div class="desc">Lokasi check-in: <a href="https://www.google.com/maps?q={{ $stopEvent->latitude }},{{ $stopEvent->longitude }}" target="_blank" rel="noreferrer">Lihat di Google Maps</a></div></div>
                            <p class="public-small">Koordinat: {{ $stopEvent->latitude }}, {{ $stopEvent->longitude }}
                                @if($stopEvent->created_at)
                                    · {{ $stopEvent->created_at->timezone('Asia/Makassar')->format('d M Y H:i') }} WITA
                                @endif
                            </p>
                        @endif
                    @endif
                    @if($stop->status !== 'Checked In' && $canCheckIn && $stop->status !== 'Cancelled')
                        <button type="button" class="public-action public-stop-checkin" data-stop-id="{{ $stop->id }}" data-stop-name="{{ $stop->name }}">📍 Check-in destinasi dengan GPS</button>
                        <form method="post" action="{{ $showNumberUrl ? route('public.spk.number.destinations.cancel', [$spk->id, $spk->spk_number, $stop->id]) : route('public.spk.destinations.cancel', [$token, $stop->id]) }}" class="mt-2">
                            @csrf
                            <div class="public-form-row"><div><label class="public-small" for="cancel-reason-{{ $stop->id }}">Alasan pembatalan (opsional)</label><input id="cancel-reason-{{ $stop->id }}" class="form-control" name="reason" maxlength="500"></div><div class="align-self-end"><button class="public-action public-action-danger" type="submit" onclick="return confirm('Batalkan destinasi ini?')">Batalkan destinasi</button></div></div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
    @endif
</div>

@if($canCheckIn)
    <div class="card shadow p-4 mt-3 mb-3">
        <h4 class="section-title mt-0">Check-in GPS</h4>
        <p>Lokasi perangkat akan dicatat saat check-in dikonfirmasi. Pastikan izin lokasi pada browser telah diaktifkan.</p>
        <div id="gpsStatus" class="public-gps-message">GPS belum diambil. Pilih destinasi lalu tekan tombol check-in.</div>
        <form method="post" action="{{ $checkinUrl }}" id="checkinForm">
            @csrf
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="accuracy_meters" id="accuracy">
            <input type="hidden" name="idempotency_key" id="idem">
            <input type="hidden" name="itinerary_stop_id" id="stopId">
            <div class="public-field"><label for="checkin-note">Catatan check-in (opsional)</label><textarea name="note" id="checkin-note" rows="2" maxlength="1000"></textarea></div>
            <button type="button" id="getGps" class="public-action public-action-secondary">Ambil GPS untuk check-in umum</button>
            <button type="submit" id="submitCheckin" class="public-action" disabled>Konfirmasi check-in umum</button>
        </form>
    </div>

    <div class="card shadow p-4 mt-3 mb-3">
        <h4 class="section-title mt-0">✚ Tambah Destinasi</h4>
        <p>Tambahkan destinasi yang dikunjungi selama perjalanan. Lokasi GPS akan dicatat saat check-in.</p>
        <form method="post" action="{{ $addDestinationUrl }}">
            @csrf
            <div class="public-field"><label for="destination-name">Nama Lokasi / Destinasi <span>*</span></label><input id="destination-name" name="name" required maxlength="150" placeholder="Contoh: Monkey Forest"></div>
            <div class="public-field"><label for="destination-address">Alamat / Lokasi <span>*</span></label><input id="destination-address" name="address" required maxlength="500" placeholder="Alamat atau lokasi destinasi"></div>
            <div class="public-field"><label for="destination-note">Deskripsi / Catatan</label><textarea id="destination-note" name="note" rows="2" maxlength="1000"></textarea></div>
            <button type="submit" class="public-action">Simpan Destinasi</button>
        </form>
    </div>

    <div class="card shadow p-4 mt-3 mb-3">
        <h4 class="section-title mt-0">Penyelesaian Layanan</h4>
        <form method="post" action="{{ $completeUrl }}">
            @csrf
            <button type="submit" class="public-action" onclick="return confirm('Tandai layanan selesai? Setelah selesai, SPK tidak dapat diubah oleh driver.')">Tandai Layanan Selesai</button>
        </form>
    </div>
@else
    <div class="public-readonly"><strong>Check-in ditutup.</strong><p class="mt-2">{{ $expired ? 'SPK telah kedaluwarsa. Halaman tetap tersedia untuk melihat detail dan riwayat, tetapi tidak dapat diubah.' : ($service->status === 'Cancelled' ? 'Layanan ini telah dibatalkan.' : ($service->status === 'Completed' ? 'Layanan ini telah selesai.' : 'Check-in hanya tersedia selama periode layanan.')) }}</p></div>
@endif

<div class="note">
    <h4 class="section-title">Ketentuan & Himbauan untuk Driver</h4>
    <ul>
        <li>Pastikan kondisi kendaraan bersih, aman, dan siap digunakan.</li>
        <li>Tiba tepat waktu sesuai jadwal penjemputan yang telah ditentukan.</li>
        <li>Selalu mengemudi dengan hati-hati dan mematuhi rambu lalu lintas.</li>
        <li>Pastikan kenyamanan penumpang menjadi prioritas.</li>
        <li>Lakukan check-in GPS di destinasi sesuai itinerary.</li>
        <li>Jaga sikap profesional dan nama baik perusahaan.</li>
    </ul>
</div>
<div class="public-footer">BALI KAMI TOUR · TRANSPORT MANAGEMENT SYSTEM · Waktu operasional WITA</div>

<script>
(() => {
    const form = document.getElementById('checkinForm');
    if (!form) return;
    const gpsButton = document.getElementById('getGps');
    const submitButton = document.getElementById('submitCheckin');
    const statusBox = document.getElementById('gpsStatus');
    const stopId = document.getElementById('stopId');
    const makeKey = () => window.crypto?.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2);
    document.getElementById('idem').value = makeKey();
    const status = (message, error = false) => {
        statusBox.textContent = message;
        statusBox.className = 'public-gps-message ' + (error ? 'public-gps-error' : 'public-gps-success');
    };
    const getLocation = () => {
        if (!navigator.geolocation) { status('Browser tidak mendukung GPS/geolocation.', true); return; }
        gpsButton.disabled = true;
        submitButton.disabled = true;
        status('Sedang mengambil lokasi GPS perangkat…');
        navigator.geolocation.getCurrentPosition(position => {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
            document.getElementById('accuracy').value = position.coords.accuracy;
            document.getElementById('idem').value = makeKey();
            status('Lokasi berhasil diperoleh. Akurasi ±' + Math.round(position.coords.accuracy) + ' meter. Konfirmasi untuk menyimpan check-in.');
            submitButton.disabled = false;
            gpsButton.disabled = false;
        }, error => {
            status(error.code === 1 ? 'Izin lokasi ditolak. Aktifkan izin lokasi pada browser.' : error.code === 2 ? 'Lokasi tidak tersedia. Aktifkan layanan lokasi perangkat.' : 'Permintaan GPS melewati batas waktu. Silakan coba lagi.', true);
            gpsButton.disabled = false;
        }, { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 });
    };
    gpsButton.addEventListener('click', () => {
        stopId.value = '';
        submitButton.textContent = 'Konfirmasi check-in umum';
        getLocation();
    });
    document.querySelectorAll('.public-stop-checkin').forEach(button => button.addEventListener('click', () => {
        stopId.value = button.dataset.stopId;
        submitButton.textContent = 'Konfirmasi check-in: ' + button.dataset.stopName;
        document.getElementById('checkinForm').scrollIntoView({behavior:'smooth', block:'center'});
        getLocation();
    }));
    form.addEventListener('submit', () => {
        submitButton.disabled = true;
        submitButton.textContent = 'Menyimpan check-in…';
    });
})();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
