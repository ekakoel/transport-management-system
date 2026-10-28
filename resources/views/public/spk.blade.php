@extends('layouts.app')
@section('title', 'SPK '.$spk->spk_number)
@section('content')
@php
    $service = $spk->service;
    $expired = $service->status === 'Expired';
    $finalStatus = in_array($service->status, ['Expired', 'Cancelled', 'Completed'], true);
    $showNumberUrl = $numberUrl ?? false;
    $spkPath = $showNumberUrl
        ? route('public.spk.number.show', [$spk->id, $spk->spk_number])
        : route('public.spk.show', $token);
@endphp

<div class="spk public-spk">
    <section class="spkcard public-spk-hero">
        <div class="public-spk-brand">
            <span class="public-spk-brand-mark">BK</span>
            <span><strong>BALI KAMI TOUR</strong><small>DIGITAL TRANSPORT SERVICE ORDER</small></span>
        </div>
        <div class="public-spk-hero-row">
            <div>
                <div class="muted">SURAT PERINTAH KERJA · DIGITAL SPK</div>
                <h1>{{ $spk->spk_number }}</h1>
                <p>Layanan {{ $service->service_code }} · Driver {{ $service->driver?->name ?? 'Belum ditentukan' }}</p>
            </div>
            <span class="public-spk-status {{ $expired ? 'is-expired' : ($service->status === 'Cancelled' ? 'is-cancelled' : (in_array($service->status, ['Completed'], true) ? 'is-completed' : 'is-active')) }}">
                {{ $service->status ?: 'Published' }}
            </span>
        </div>
        @if($expired)
            <div class="public-spk-expired-banner"><strong>SPK EXPIRED</strong><span>Tanggal layanan telah berakhir. Halaman ini tetap dapat dibuka untuk melihat detail dan riwayat, tetapi check-in sudah ditutup.</span></div>
        @endif
    </section>

    @if(session('success'))
        <div class="alert public-spk-alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert error public-spk-alert-error" role="alert">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <section class="spkcard">
        <h3>Detail Layanan</h3>
        <div class="public-spk-detail-grid">
            <div><span>Nomor SPK</span><strong>{{ $spk->spk_number }}</strong></div>
            <div><span>Kode layanan</span><strong>{{ $service->service_code }}</strong></div>
            <div><span>Tanggal layanan</span><strong>{{ $service->service_date->format('d M Y') }} – {{ $service->service_end_date->format('d M Y') }}</strong></div>
            <div><span>Jenis layanan</span><strong>{{ $service->serviceType?->name ?? '—' }}</strong></div>
            <div><span>Pelanggan</span><strong>{{ $service->customer_name }}</strong></div>
            <div><span>Telepon pelanggan</span><strong>{{ $service->customer_phone ?: '—' }}</strong></div>
            <div><span>Lokasi penjemputan</span><strong>{{ $service->pickup_location }}</strong></div>
            <div><span>Driver</span><strong>{{ $service->driver?->name ?? '—' }}</strong></div>
            <div><span>Kendaraan</span><strong>{{ $service->vehicle?->name ?? '—' }}</strong></div>
            <div><span>Nomor kendaraan</span><strong>{{ $service->vehicle?->plate_number ?? '—' }}</strong></div>
            @if($service->start_time)<div><span>Waktu mulai</span><strong>{{ substr((string) $service->start_time, 0, 5) }} WITA</strong></div>@endif
            @if($service->end_time)<div><span>Waktu selesai</span><strong>{{ substr((string) $service->end_time, 0, 5) }} WITA</strong></div>@endif
        </div>
        @if($service->notes)<div class="public-spk-note"><span>Catatan layanan</span><p>{{ $service->notes }}</p></div>@endif
    </section>

    @if($canCheckIn)
        <section class="spkcard public-spk-gps-panel" id="gps-checkin">
            <h3>Check-in GPS</h3>
            <p>Check-in dilakukan pada setiap destinasi. Izinkan akses lokasi pada browser agar koordinat dan akurasi GPS perangkat dicatat untuk laporan operasional.</p>
            <div id="gpsStatus" class="alert error" role="status">GPS belum diambil. Pilih destinasi lalu tekan tombol check-in pada destinasi tersebut.</div>
            <div class="public-spk-gps-actions">
                <button class="btn secondary spkbutton" type="button" id="getGps">Ambil lokasi GPS untuk check-in umum</button>
            </div>
            <form method="post" action="{{ $showNumberUrl ? route('public.spk.number.checkin', [$spk->id, $spk->spk_number]) : route('public.spk.checkin', $token) }}" id="checkinForm" class="public-spk-checkin-form">
                @csrf
                <input type="hidden" name="latitude" id="lat">
                <input type="hidden" name="longitude" id="lon">
                <input type="hidden" name="accuracy_meters" id="accuracy">
                <input type="hidden" name="idempotency_key" id="idem">
                <div class="field"><label>Catatan check-in (opsional)</label><textarea name="note" maxlength="1000" placeholder="Catatan singkat, jika diperlukan"></textarea></div>
                <button class="btn spkbutton" id="submitCheckin" disabled>Konfirmasi check-in umum</button>
            </form>
        </section>
    @else
        <section class="spkcard public-spk-readonly">
            <h3>Check-in ditutup</h3>
            <p>{{ $expired ? 'Tanggal layanan sudah terlewat. SPK hanya dapat dilihat dan riwayatnya tetap tersedia.' : ($service->status === 'Cancelled' ? 'Layanan ini telah dibatalkan.' : ($service->status === 'Completed' ? 'Layanan ini telah ditandai selesai.' : 'Check-in tersedia pada tanggal layanan: '.$service->service_date->format('d M Y').' sampai '.$service->service_end_date->format('d M Y').'.')) }}</p>
        </section>
    @endif

    <section class="spkcard public-spk-itinerary">
        <div class="public-spk-section-heading"><h3>Itinerary & Check-in Destinasi</h3><span class="public-spk-count">{{ $service->stops->count() }} destinasi</span></div>
        <p class="public-spk-section-help">Status setiap destinasi diperbarui setelah check-in berhasil disimpan.</p>
        @forelse($service->stops as $stop)
            <article class="public-spk-stop {{ $stop->status === 'Checked In' ? 'is-checked-in' : '' }} {{ $stop->status === 'Cancelled' ? 'is-stop-cancelled' : '' }}">
                <div class="public-spk-stop-number">{{ str_pad((string) $stop->sort_order, 2, '0', STR_PAD_LEFT) }}</div>
                <div class="public-spk-stop-body">
                    <div class="public-spk-stop-heading">
                        <div><h4>{{ $stop->name }}</h4><p>{{ $stop->address ?: 'Alamat belum dicatat.' }}</p></div>
                        <div class="public-spk-stop-badges">
                            <span class="public-spk-stop-status">{{ $stop->status }}</span>
                            @if($stop->added_by_driver)<span class="badge blue">Tambahan driver</span>@endif
                        </div>
                    </div>
                    @if($stop->note)<p class="public-spk-stop-note">{{ $stop->note }}</p>@endif
                    @if($canCheckIn && !in_array($stop->status, ['Checked In', 'Cancelled'], true))
                        <button type="button" class="btn public-spk-destination-checkin" data-stop-id="{{ $stop->id }}" data-stop-name="{{ $stop->name }}">Check-in destinasi ini dengan GPS</button>
                        <span class="public-spk-stop-help">GPS perangkat dicatat saat Anda mengonfirmasi check-in.</span>
                    @elseif($stop->status === 'Checked In')
                        <span class="public-spk-checked-label">✓ Check-in tercatat</span>
                    @endif
                    @if($canCheckIn && !in_array($stop->status, ['Checked In', 'Cancelled'], true))
                        <form method="post" action="{{ $showNumberUrl ? route('public.spk.number.destinations.cancel', [$spk->id, $spk->spk_number, $stop->id]) : route('public.spk.destinations.cancel', [$token, $stop->id]) }}" class="public-spk-cancel-form">
                            @csrf
                            <input name="reason" maxlength="500" placeholder="Alasan pembatalan (opsional)">
                            <button class="btn danger" type="submit" onclick="return confirm('Batalkan kunjungan destinasi ini?')">Batalkan destinasi</button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="public-spk-empty"><strong>Belum ada destinasi</strong><p>Itinerary belum memiliki destinasi yang tercatat.</p></div>
        @endforelse
    </section>

    @if($canCheckIn)
        <section class="spkcard public-spk-add-destination">
            <h3>Tambah Destinasi</h3>
            <p>Gunakan bagian ini bila ada perubahan rute yang perlu dicatat pada SPK.</p>
            <form method="post" action="{{ $showNumberUrl ? route('public.spk.number.destinations.add', [$spk->id, $spk->spk_number]) : route('public.spk.destinations.add', $token) }}">
                @csrf
                <div class="field"><label>Nama destinasi *</label><input name="name" required maxlength="150"></div>
                <div class="field"><label>Alamat / lokasi *</label><input name="address" required maxlength="500"></div>
                <div class="field"><label>Catatan (opsional)</label><input name="note" maxlength="1000"></div>
                <button class="btn secondary spkbutton" type="submit">Simpan destinasi</button>
            </form>
        </section>
    @endif

    <section class="spkcard public-spk-emergency">
        <h3>Kontak Operasional</h3>
        <p class="public-spk-emergency-name">{{ $spk->emergency_name ?: 'Operasional Bali Kami Tour' }}</p>
        <div class="public-spk-emergency-actions">
            @if($spk->emergency_phone)<a class="btn spkbutton" href="tel:{{ preg_replace('/[^0-9+]/', '', $spk->emergency_phone) }}">Telepon · {{ $spk->emergency_phone }}</a>@endif
            @if($spk->emergency_whatsapp)<a class="btn secondary spkbutton" href="https://wa.me/{{ preg_replace('/\D/', '', $spk->emergency_whatsapp) }}" rel="noreferrer">WhatsApp operasional</a>@endif
        </div>
        <p class="public-spk-small">SPK digital · Waktu aktivitas dicatat oleh server dalam zona waktu operasional.</p>
    </section>

    @if($canCheckIn)
        <form method="post" action="{{ $showNumberUrl ? route('public.spk.number.complete', [$spk->id, $spk->spk_number]) : route('public.spk.complete', $token) }}" class="public-spk-complete-form">
            @csrf
            <button class="btn public-spk-complete-button" type="submit" onclick="return confirm('Tandai layanan selesai? Setelah selesai, SPK tidak dapat diubah oleh driver.')">Tandai layanan selesai</button>
        </form>
    @endif
    <footer class="public-spk-footer">BALI KAMI TOUR · TRANSPORT MANAGEMENT SYSTEM · {{ now('Asia/Makassar')->format('d M Y H:i') }} WITA</footer>
</div>

<script>
(() => {
    const gpsButton = document.getElementById('getGps');
    const submitButton = document.getElementById('submitCheckin');
    const statusBox = document.getElementById('gpsStatus');
    const form = document.getElementById('checkinForm');
    if (!gpsButton || !submitButton || !statusBox || !form) return;

    const newKey = () => (window.crypto && crypto.randomUUID)
        ? crypto.randomUUID()
        : 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
            const r = Math.random() * 16 | 0;
            return (c === 'x' ? r : (r & 3 | 8)).toString(16);
        });
    document.getElementById('idem').value = newKey();

    const setStatus = (message, isError = false) => {
        statusBox.className = isError ? 'alert error' : 'alert public-spk-gps-success';
        statusBox.textContent = message;
    };

    const requestGps = () => {
        if (!('geolocation' in navigator)) {
            setStatus('Browser ini tidak mendukung GPS. Gunakan browser modern dan aktifkan lokasi.', true);
            return;
        }
        setStatus('Sedang mengambil lokasi GPS perangkat…');
        gpsButton.disabled = true;
        navigator.geolocation.getCurrentPosition(position => {
            document.getElementById('lat').value = position.coords.latitude;
            document.getElementById('lon').value = position.coords.longitude;
            document.getElementById('accuracy').value = position.coords.accuracy;
            document.getElementById('idem').value = newKey();
            setStatus('Lokasi diperoleh. Akurasi perangkat ±' + Math.round(position.coords.accuracy) + ' meter. Konfirmasi check-in untuk menyimpan koordinat.');
            submitButton.disabled = false;
            gpsButton.disabled = false;
        }, error => {
            const message = error.code === 1
                ? 'Izin lokasi ditolak. Aktifkan izin lokasi pada pengaturan browser.'
                : error.code === 2
                    ? 'Lokasi tidak tersedia. Pastikan GPS/lokasi perangkat aktif.'
                    : 'Permintaan GPS terlalu lama. Coba lagi di area dengan sinyal lebih baik.';
            setStatus(message, true);
            gpsButton.disabled = false;
            submitButton.disabled = true;
        }, { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 });
    };

    gpsButton.addEventListener('click', () => {
        document.getElementById('itinerary_stop_id')?.remove();
        submitButton.textContent = 'Konfirmasi check-in umum';
        requestGps();
    });

    document.querySelectorAll('[data-stop-id]').forEach(button => {
        button.addEventListener('click', () => {
            let stopInput = document.getElementById('itinerary_stop_id');
            if (!stopInput) {
                stopInput = document.createElement('input');
                stopInput.type = 'hidden';
                stopInput.name = 'itinerary_stop_id';
                stopInput.id = 'itinerary_stop_id';
                form.appendChild(stopInput);
            }
            stopInput.value = button.dataset.stopId;
            submitButton.textContent = 'Konfirmasi check-in: ' + button.dataset.stopName;
            setStatus('Mengambil GPS untuk destinasi: ' + button.dataset.stopName + '…');
            document.getElementById('gps-checkin').scrollIntoView({ behavior: 'smooth', block: 'center' });
            requestGps();
        });
    });

    form.addEventListener('submit', () => {
        submitButton.disabled = true;
        submitButton.textContent = 'Menyimpan check-in…';
    });
})();
</script>
@endsection
