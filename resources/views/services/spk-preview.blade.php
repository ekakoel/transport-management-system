@extends('layouts.app')

@section('title', 'Detail SPK')

@section('content')
    @php
        $statusClass = match ($service->status) {
            'Draft' => 'draft',
            'Published' => 'published',
            'In Progress' => 'progress',
            'Completed' => 'completed',
            'Cancelled' => 'cancelled',
            'Expired' => 'expired',
            default => 'default',
        };
    @endphp
    <header>
        <div class="main-header">
            <div class="service-show-heading">
                <div class="service-show-eyebrow">
                    <span></span>
                    TRANSPORT OPERATIONS / DETAIL SPK
                </div>

                <h1>{{ $service->service_code }}</h1>

                <p>
                    Nomor SPK:
                    <strong>{{ $service->spk?->spk_number ?: 'Belum diterbitkan' }}</strong>
                    <span class="service-show-separator">Â·</span>
                    Detail layanan dan laporan driver
                </p>
            </div>

            <a href="{{ route('services.index') }}" class="service-show-back">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M19 12H5m7 7-7-7 7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                Semua Layanan
            </a>
            @if ($service->spk)
                <a href="{{ route('services.spk.preview', $service) }}" class="service-show-back">
                    Lihat SPK
                </a>
            @endif
        </div>
    </header>
    <div class="service-show-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
                {{-- Public SPK Link --}}
                @if ($newUrl)
                    <section class="service-show-public-link">
                        <div class="service-show-public-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M10 13a5 5 0 0 0 7.1 0l3-3A5 5 0 0 0 13 2.9l-1.7 1.7" stroke="currentColor"
                                    stroke-width="1.8" stroke-linecap="round" />
                                <path d="M14 11a5 5 0 0 0-7.1 0l-3 3A5 5 0 0 0 11 21.1l1.7-1.7" stroke="currentColor"
                                    stroke-width="1.8" stroke-linecap="round" />
                            </svg>
                        </div>

                        <div class="service-show-public-content">
                            <h2>Tautan Publik SPK</h2>
                            <p>Kirim tautan ini kepada driver agar dapat membuka SPK dan mengirim laporan aktivitas.</p>

                            <div class="service-show-url-row">
                                <a href="{{ $newUrl }}" target="_blank" rel="noopener noreferrer"
                                    class="service-show-url" id="publicSpkUrl">{{ $newUrl }}</a>

                                <button type="button" class="service-show-copy-btn" id="copyPublicSpkUrl"
                                    data-url="{{ $newUrl }}">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="8" y="8" width="12" height="12" rx="2" stroke="currentColor"
                                            stroke-width="1.7" />
                                        <path d="M16 8V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h3"
                                            stroke="currentColor" stroke-width="1.7" />
                                    </svg>
                                    <span>Salin Tautan</span>
                                </button>
                            </div>

                            <div class="service-show-copy-feedback" id="copyPublicSpkFeedback" role="status"
                                aria-live="polite"></div>

                            <div class="service-show-security-note">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 3 20 6v5c0 5-3.4 8-8 10-4.6-2-8-5-8-10V6l8-3Z" stroke="currentColor"
                                        stroke-width="1.6" stroke-linejoin="round" />
                                    <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                Demi keamanan, token mentah hanya ditampilkan saat SPK baru dibuat.
                                Simpan dan bagikan tautan ini dengan aman.
                            </div>
                        </div>
                    </section>
                @endif

                {{-- Summary Cards --}}
                <div class="service-show-summary">

                    <section class="service-show-card">
                        <div class="service-show-card-top">
                            <div class="service-show-card-icon service-show-blue">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="3.5" y="5" width="17" height="15.5" rx="2" stroke="currentColor"
                                        stroke-width="1.7" />
                                    <path d="M7.5 3v4M16.5 3v4M3.5 9.5h17" stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" />
                                </svg>
                            </div>
                            <span class="service-show-mini-label">SERVICE STATUS</span>
                        </div>

                        <div class="service-show-card-label">Status Layanan</div>
                        <div class="service-show-status-row">
                            <span class="service-show-status service-show-status-{{ $statusClass }}">
                                <span></span>
                                {{ $service->status }}
                            </span>
                        </div>

                        <div class="service-show-card-divider"></div>

                        <div class="service-show-detail-label">Periode Layanan</div>
                        <div class="service-show-summary-value">
                            {{ \App\Support\DateFormatter::format($service->service_date) }}
                        </div>
                        <div class="service-show-summary-sub">
                            sampai {{ \App\Support\DateFormatter::format($service->service_end_date) }}
                        </div>

                        <div class="service-show-card-bottom">
                            <span>Tipe Layanan</span>
                            <strong>{{ $service->serviceType?->name ?? 'â€”' }}</strong>
                        </div>
                    </section>

                    <section class="service-show-card">
                        <div class="service-show-card-top">
                            <div class="service-show-card-icon service-show-green">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="10" cy="7" r="4" stroke="currentColor" stroke-width="1.7" />
                                    <path d="M3 21v-1.5A4.5 4.5 0 0 1 7.5 15h5A4.5 4.5 0 0 1 17 19.5V21"
                                        stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                    <path d="M16 4a4 4 0 0 1 0 7.5m2 3a4.5 4.5 0 0 1 2 3.5V21" stroke="currentColor"
                                        stroke-width="1.7" stroke-linecap="round" />
                                </svg>
                            </div>
                            <span class="service-show-mini-label">DRIVER ASSIGNMENT</span>
                        </div>

                        <div class="service-show-card-label">Driver Bertugas</div>
                        <div class="service-show-summary-value">
                            {{ $service->driver?->name ?? 'Belum ditugaskan' }}
                        </div>
                        <div class="service-show-summary-sub">
                            {{ $service->driver?->phone ?? 'Nomor telepon tidak tersedia' }}
                        </div>

                        <div class="service-show-card-divider"></div>

                        <div class="service-show-detail-label">Kendaraan</div>
                        <div class="service-show-summary-value">
                            {{ $service->vehicle?->name ?? 'Belum ditentukan' }}
                        </div>
                        <div class="service-show-summary-sub">
                            {{ $service->vehicle?->plate_number ?? 'Nomor polisi tidak tersedia' }}
                        </div>
                    </section>
                </div>

                {{-- Trip Details --}}
                <section class="service-show-panel">
                    <div class="service-show-panel-header">
                        <div class="service-show-panel-heading">
                            <div class="service-show-panel-icon service-show-blue">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 5h16M4 12h16M4 19h10" stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" />
                                    <circle cx="18" cy="18" r="3" stroke="currentColor"
                                        stroke-width="1.7" />
                                </svg>
                            </div>
                            <div>
                                <h2>Detail Perjalanan</h2>
                                <p>Informasi pelanggan, penjemputan, dan instruksi layanan.</p>
                            </div>
                        </div>
                    </div>

                    <div class="service-show-details-grid">
                        <div class="service-show-detail-item">
                            <span class="service-show-detail-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="10" cy="7" r="4" stroke="currentColor"
                                        stroke-width="1.7" />
                                    <path d="M3 21v-1.5A4.5 4.5 0 0 1 7.5 15h5A4.5 4.5 0 0 1 17 19.5V21"
                                        stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                </svg>
                            </span>
                            <div>
                                <span class="service-show-detail-label">Pelanggan / Pemesan</span>
                                <strong>{{ $service->customer_name }}</strong>
                                <small>{{ $service->customer_phone ?: 'Nomor telepon tidak tersedia' }}</small>
                            </div>
                        </div>

                        

                        <div class="service-show-detail-item">
                            <span class="service-show-detail-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor"
                                        stroke-width="1.7" />
                                    <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" />
                                </svg>
                            </span>
                            <div>
                                <span class="service-show-detail-label">Waktu Layanan</span>
                                <strong>
                                    {{ \App\Support\DateFormatter::format($service->service_date, $service->start_time) ?: 'â€”' }}
                                    <span class="service-show-time-separator">â€“</span>
                                    {{ \App\Support\DateFormatter::format($service->service_end_date, $service->end_time) ?: 'â€”' }}
                                </strong>
                                <small>Waktu setempat Bali (WITA)</small>
                            </div>
                        </div>

                        <div class="service-show-detail-item">
                            <span class="service-show-detail-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <div>
                                <span class="service-show-detail-label">Catatan Layanan</span>
                                <strong class="service-show-notes">
                                    {{ $service->notes ?: 'Tidak ada catatan tambahan.' }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="service-show-panel">
                    <div class="service-show-panel-header"><div class="service-show-panel-heading"><div><h2>Pemilik Reservation &amp; Daftar Tamu</h2><p>Informasi internal kasus layanan.</p></div></div></div>
                    <p><strong>Pemilik Reservation:</strong> {{ $service->reservationOwner?->name ?? 'Belum ditetapkan' }} @if($service->reservationOwner?->email) ({{ $service->reservationOwner->email }}) @endif</p>
                    @if($service->reservation)<p><strong>Nomor Reservation:</strong> {{ $service->reservation->reservation_number }}</p>@endif
                    <h3>Daftar Tamu ({{ $service->guests->count() }})</h3>
                    @forelse($service->guests as $guest)
                        <p>{{ $guest->name }} · {{ $guest->gender }} · {{ $guest->age_category }} · {{ $guest->phone ?: '—' }}</p>
                    @empty <p>Belum ada data tamu.</p> @endforelse
                </section>

                {{-- Itinerary --}}
                <section class="service-show-panel">
                    <div class="service-show-panel-header">
                        <div class="service-show-panel-heading">
                            <div class="service-show-panel-icon service-show-orange">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 21s7-6.1 7-12a7 7 0 1 0-14 0c0 5.9 7 12 7 12Z" stroke="currentColor"
                                        stroke-width="1.7" />
                                    <circle cx="12" cy="9" r="2.5" stroke="currentColor"
                                        stroke-width="1.7" />
                                </svg>
                            </div>
                            <div>
                                <h2>Itinerary Perjalanan</h2>
                                <p>Daftar destinasi dan status setiap pemberhentian.</p>
                            </div>
                        </div>

                        <span class="service-show-count">
                            {{ $service->stops->count() }} destinasi
                        </span>
                    </div>

                    @forelse ($service->stops as $stop)
                        <div class="service-show-stop">
                            <div class="service-show-stop-track">
                                <span class="service-show-stop-number">{{ $stop->sort_order }}</span>
                                @if (!$loop->last)
                                    <span class="service-show-stop-line"></span>
                                @endif
                            </div>

                            <div class="service-show-stop-content">
                                <div class="service-show-stop-heading">
                                    <h3>{{ $stop->name }}</h3>
                                    <div class="service-show-stop-badges">
                                        <span class="service-show-stop-status">
                                            {{ $stop->status }}
                                        </span>

                                        @if ($stop->added_by_driver)
                                            <span class="service-show-driver-added">
                                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8"
                                                        stroke-linecap="round" />
                                                </svg>
                                                Tambahan Driver
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <p><strong>Waktu kunjungan:</strong> {{ $stop->visit_time ? \App\Support\DateFormatter::format($stop->visit_time, null, 'H:i') . ' WITA' : 'Belum ditentukan' }}</p>
                                @if ($stop->google_maps_url)
                                    <p><a href="{{ $stop->google_maps_url }}" target="_blank" rel="noopener noreferrer">Buka destinasi di Google Maps</a></p>
                                @endif
                                <p>{{ $stop->address ?: 'Tidak ada keterangan lokasi.' }}</p>
                                <span class="service-show-stop-sequence">
                                    DESTINASI {{ str_pad((string) $stop->sort_order, 2, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="service-show-empty">
                            <div class="service-show-empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 21s7-6.1 7-12a7 7 0 1 0-14 0c0 5.9 7 12 7 12Z" stroke="currentColor"
                                        stroke-width="1.7" />
                                    <circle cx="12" cy="9" r="2.5" stroke="currentColor"
                                        stroke-width="1.7" />
                                </svg>
                            </div>
                            <h3>Belum ada destinasi</h3>
                            <p>Itinerary belum memiliki destinasi yang tercatat.</p>
                        </div>
                    @endforelse
                </section>

                {{-- Driver Activity Log --}}
                <section class="service-show-panel service-show-activity-panel">
                    <div class="service-show-panel-header">
                        <div class="service-show-panel-heading">
                            <div class="service-show-panel-icon service-show-purple">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 19V5m0 14h16M8 15l3-4 3 2 5-7" stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div>
                                <h2>Laporan Aktivitas Driver</h2>
                                <p>Riwayat aktivitas dan data lokasi yang dikirim driver.</p>
                            </div>
                        </div>

                        <span class="service-show-count">
                            {{ $service->spk?->events?->count() ?? 0 }} aktivitas
                        </span>
                    </div>

                    <div class="service-show-table-scroll">
                        <table class="service-show-activity-table">
                            <thead>
                                <tr>
                                    <th>Waktu Server (WITA)</th>
                                    <th>Aktivitas</th>
                                    <th>Destinasi</th>
                                    <th>Lokasi GPS</th>
                                    <th>Akurasi</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($service->spk?->events ?? [] as $e)
                                    <tr>
                                        <td>
                                            <div class="service-show-event-date">
                                                {{ \App\Support\DateFormatter::format($e->created_at) ?? 'â€”' }}
                                            </div>
                                            <div class="service-show-event-time">
                                                {{ \App\Support\DateFormatter::format($e->created_at, null, 'H:i:s') ?? 'â€”' }}
                                            </div>
                                        </td>

                                        <td>
                                            <span class="service-show-event-type">
                                                {{ ucwords(str_replace('_', ' ', $e->event_type)) }}
                                            </span>
                                        </td>

                                        <td>{{ $e->stop?->name ?: 'â€”' }}</td>

                                        <td>
                                            @if ($e->latitude !== null && $e->longitude !== null)
                                                <a class="service-show-map-link" target="_blank"
                                                    rel="noopener noreferrer"
                                                    href="https://www.openstreetmap.org/?mlat={{ $e->latitude }}&mlon={{ $e->longitude }}#map=16/{{ $e->latitude }}/{{ $e->longitude }}">
                                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"
                                                            stroke="currentColor" stroke-width="1.7" />
                                                        <circle cx="12" cy="10" r="2.5"
                                                            stroke="currentColor" stroke-width="1.7" />
                                                    </svg>
                                                    Lihat Lokasi
                                                </a>
                                            @else
                                                <span class="service-show-no-location">Tidak tersedia</span>
                                            @endif
                                        </td>

                                        <td>
                                            {{ $e->accuracy_meters !== null ? number_format((float) $e->accuracy_meters, 0) . ' m' : 'â€”' }}
                                        </td>

                                        <td class="service-show-event-note">
                                            {{ $e->note ?: 'â€”' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="service-show-empty service-show-empty-compact">
                                                <div class="service-show-empty-icon">
                                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M4 19V5m0 14h16M8 15l3-4 3 2 5-7" stroke="currentColor"
                                                            stroke-width="1.8" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                                <h3>Belum ada aktivitas driver</h3>
                                                <p>Laporan aktivitas akan tampil di sini setelah driver mengirimkannya.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <aside class="tms-info-panel" aria-label="Informasi Ringkasan SPK">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Ringkasan SPK</h2>
                    <p>Informasi penting untuk layanan yang sedang dibuka.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Status layanan</span>
                        <strong class="tms-info-value">{{ $service->status }}</strong>
                        <span class="tms-info-description">Status terkini pada layanan ini.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Tanggal mulai</span>
                        <strong class="tms-info-value">{{ \App\Support\DateFormatter::format($service->service_date) ?? 'â€”' }}</strong>
                        <span class="tms-info-description">Tanggal layanan.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Tanggal selesai</span>
                        <strong class="tms-info-value">{{ \App\Support\DateFormatter::format($service->service_end_date) ?? 'â€”' }}</strong>
                        <span class="tms-info-description">Tanggal akhir layanan.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Driver</span>
                        <strong class="tms-info-value">{{ $service->driver?->name ?? 'Belum ditugaskan' }}</strong>
                        <span class="tms-info-description">Driver yang ditetapkan.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Kendaraan</span>
                        <strong
                            class="tms-info-value">{{ $service->vehicle?->plate_number ?? 'Belum ditentukan' }}</strong>
                        <span class="tms-info-description">Nomor kendaraan.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Jumlah destinasi</span>
                        <strong class="tms-info-value">{{ $service->stops->count() }}</strong>
                        <span class="tms-info-description">Termasuk destinasi tambahan yang tersimpan.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Aktivitas SPK</span>
                        <strong class="tms-info-value">{{ $service->spk?->events?->count() ?? 0 }}</strong>
                        <span class="tms-info-description">Riwayat event SPK yang tersedia.</span>
                    </div>
                </div>
                <div class="tms-info-panel-footer"><a href="{{ route('services.index') }}">&#8592; Kembali ke Daftar Layanan</a></div>
                        layanan</a></div>
            </aside>
        </div>
    </div>

    @if ($newUrl)
        <script>
            (() => {
                const copyButton = document.getElementById('copyPublicSpkUrl');
                const feedback = document.getElementById('copyPublicSpkFeedback');

                if (!copyButton || !feedback) return;

                copyButton.addEventListener('click', async () => {
                    const url = copyButton.dataset.url;
                    const label = copyButton.querySelector('span');

                    try {
                        if (navigator.clipboard && window.isSecureContext) {
                            await navigator.clipboard.writeText(url);
                        } else {
                            const temporaryInput = document.createElement('textarea');
                            temporaryInput.value = url;
                            temporaryInput.setAttribute('readonly', '');
                            temporaryInput.style.position = 'fixed';
                            temporaryInput.style.opacity = '0';
                            document.body.appendChild(temporaryInput);
                            temporaryInput.select();

                            const copied = document.execCommand('copy');
                            temporaryInput.remove();

                            if (!copied) throw new Error('Copy failed');
                        }

                        feedback.textContent = 'Tautan berhasil disalin.';
                        label.textContent = 'Tersalin!';
                    } catch {
                        feedback.textContent =
                            'Tidak dapat menyalin otomatis. Silakan salin tautan secara manual.';
                    }

                    window.setTimeout(() => {
                        feedback.textContent = '';
                        label.textContent = 'Salin Tautan';
                    }, 3500);
                });
            })();
        </script>
    @endif
@endsection

