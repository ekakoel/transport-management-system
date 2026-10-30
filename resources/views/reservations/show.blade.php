@extends('layouts.app')
@section('title', 'Detail Permintaan')
@section('content')
    @php
        $serviceType = match ($reservation->serviceType?->name) {
            'Airport Transfer' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 15.5 10 13l-3-6 2-.7 5 5 4.5-1.5c1.2-.4 2.2.1 2.5 1s-.4 1.7-1.5 2.1L5 18l-2.5-.5z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /><path d="M3 21h18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /></svg>',
            'Full Day Charter' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="7" r="3.5" stroke="currentColor" stroke-width="1.7" /><path d="M12 1.5v1M12 11.5v1M6.5 7h-1M18.5 7h-1M8.1 3.1l-.7-.7M16.6 11.6l-.7-.7M15.9 3.1l.7-.7M7.4 11.6l.7-.7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /><path d="M3 16h13l3 2.5V21H3z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" /><circle cx="7" cy="20" r="1.5" fill="currentColor" /><circle cx="16" cy="20" r="1.5" fill="currentColor" /></svg>',
            'Hourly Charter' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="9" r="6.5" stroke="currentColor" stroke-width="1.7" /><path d="M12 5.5v3.8l2.5 1.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /><path d="M3 18h13l3 2.5V22H3z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" /><circle cx="7" cy="21" r="1.2" fill="currentColor" /><circle cx="16" cy="21" r="1.2" fill="currentColor" /></svg>',
            'Point to Point' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="5.5" cy="6" r="2.5" stroke="currentColor" stroke-width="1.7" /><circle cx="18.5" cy="18" r="2.5" stroke="currentColor" stroke-width="1.7" /><path d="M5.5 8.5v3a2 2 0 0 0 2 2h9a2 2 0 0 1 2 2v0" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /><path d="m15.5 13.5 3 2-3 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /></svg>',
            'Tour Transport' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m2.5 13 6-7 4 4 2.5-2.5 6.5 7.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /><circle cx="17.5" cy="5" r="1.5" stroke="currentColor" stroke-width="1.7" /><path d="M3 15h15l2 2v3H3z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" /><path d="M6 15v3h4v-3M12 15v3h4v-3" stroke="currentColor" stroke-width="1.5" /><circle cx="7" cy="20" r="1.5" fill="currentColor" /><circle cx="17" cy="20" r="1.5" fill="currentColor" /></svg>',
            default => 'default',
        };
        $captionServiceType = match ($reservation->serviceType?->name) {
            'Airport Transfer' => 'Layanan antar dari atau menuju bandara.',
            'Full Day Charter' => 'Layanan transportasi privat seharian penuh sesuai rencana perjalanan.',
            'Hourly Charter' => 'Layanan transportasi privat berdasarkan durasi per jam.',
            'Point to Point' => 'Layanan antar jemput langsung dari lokasi penjemputan ke tujuan yang ditentukan.',
            'Tour Transport' => 'Layanan transportasi privat untuk wisata sesuai dengan destinasi yang ditentukan.',
            default => 'default'
        };
        $statusClass = match ($reservation->status) {
            'Rejected' => 'resv-rejected',
            'Cancelled' => 'resv-cancelled',
            'Approved' => 'resv-approved',
            'Pending' => 'resv-pending',
            default => 'default',
        };
    @endphp
    <header>
        <div class="main-header">
            <div class="services-heading">
                <h1>{{ $reservation->reservation_number }}</h1>
                <p class="resv-muted">Diajukan {{ \App\Support\DateFormatter::format($reservation->created_at, null, 'd M Y H:i') }} · Pemohon: {{ $reservation->requester?->name }}</p>
            </div>
            @if ($isAdmin)
                <a href="{{ route('admin.reservations.index') }}" class="service-show-back">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M19 12H5m7 7-7-7 7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Kembali ke daftar permintaan
                </a>
            @else
                <a href="{{ route('reservations.index') }}" class="service-show-back">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M19 12H5m7 7-7-7 7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Kembali ke daftar permintaan
                </a>
            @endif
        </div>
    </header>
    <div class="services-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
                <section class="service-show-panel">
                    <div class="service-show-panel-header">
                        <div class="service-show-panel-heading">
                            <div class="service-show-panel-icon service-show-blue">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M7 3.5h7l5 5V14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M14 3.5v5h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M7 3.5H5a1.5 1.5 0 0 0-1.5 1.5v14A1.5 1.5 0 0 0 5 20.5h8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M7 12h7M7 15.5h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                    <circle cx="17.5" cy="17.5" r="3.5" stroke="currentColor" stroke-width="1.7"/>
                                    <path d="M17.5 15.8v3.4M15.8 17.5h3.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="resv-section-title">Informasi Permintaan Layanan</h2>
                                <p>Detail permintaan, penjemputan, dan instruksi layanan.</p>
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
                                <strong>{{ $reservation->customer_name }}</strong>
                                <small>{{ $reservation->customer_phone ?: 'Nomor telepon tidak tersedia' }}</small>
                            </div>
                        </div>
                        <div class="service-show-detail-item">
                            <span class="service-show-detail-icon">
                                {!! $serviceType ?? '—' !!}
                            </span>
                            <div>
                                <span class="service-show-detail-label">Type Layanan</span>
                                <strong>
                                    {{ $reservation->serviceType?->name ?? '—' }} ({{ $reservation->guests->count() }} pax)
                                </strong>
                                <small>{{ $captionServiceType }}</small>
                            </div>
                        </div>
                        <div class="service-show-detail-item">
                            <span class="service-show-detail-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="16" rx="2"
                                        stroke="currentColor" stroke-width="1.7" />
                                    <path d="M16 3v4M8 3v4M3 10h18"
                                        stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                    <path d="M8 14h.01M12 14h.01M16 14h.01M8 17h.01M12 17h.01"
                                        stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
                                </svg>
                            </span>
                            <div>
                                <span class="service-show-detail-label">Tanggal Mulai</span>
                                <strong>
                                    {{ \App\Support\DateFormatter::format($reservation->service_date, $reservation->start_time) }}
                                    <span class="service-show-time-separator">&#8594;</span>
                                    {{ \App\Support\DateFormatter::format($reservation->service_end_date, $reservation->end_time) }}
                                </strong>
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
                                <strong>
                                    {{ $reservation->notes ?: '—' }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="service-show-panel">
                    <div class="service-show-panel-header">
                        <div class="service-show-panel-heading">
                            <div class="service-show-panel-icon service-show-blue">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="9" cy="8" r="3.5"
                                        stroke="currentColor" stroke-width="1.7" />
                                    <path d="M2.5 20v-1.5a6.5 6.5 0 0 1 13 0V20"
                                        stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                    <path d="M16 5.2a3.5 3.5 0 0 1 0 6.6M18 14a5.5 5.5 0 0 1 3.5 5.1V20"
                                        stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="resv-section-title">Daftar Tamu</h2>
                                <p>{{ $reservation->guests->count() }} pax</p>
                            </div>
                        </div>
                    </div>
                    <div class="services-table-scroll">
                        <table class="services-table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Usia</th>
                                    <th>Kontak</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reservation->guests as $guest)
                                    <tr>
                                        <td>
                                            <div class="services-primary-text">{{ $guest->name }}</div>
                                        </td>
                                        <td>
                                            <div class="services-primary-text">{{ $guest->gender }}</div>
                                        </td>
                                        <td>
                                            <div class="services-primary-text">{{ $guest->age_category }}</div>
                                        </td>
                                        <td>
                                            <div class="services-primary-text">{{ $guest->phone ? $guest->phone:"-"; }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="empty-value">Data tamu tidak tersedia.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
                <section class="service-show-panel">
                    <div class="service-show-panel-header">
                        <div class="service-show-panel-heading">
                            <div class="service-show-panel-icon service-show-blue">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 21s7-6.1 7-12a7 7 0 1 0-14 0c0 5.9 7 12 7 12Z" stroke="currentColor"
                                        stroke-width="1.7" />
                                    <circle cx="12" cy="9" r="2.5" stroke="currentColor"
                                        stroke-width="1.7" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="resv-section-title">Itinerary</h2>
                                <p>{{ \App\Support\DateFormatter::format($reservation->service_date, $reservation->start_time) }}
                                    <span class="service-show-time-separator">&#8594;</span>
                                    {{ \App\Support\DateFormatter::format($reservation->service_end_date, $reservation->end_time) }}
                                </p>
                            </div>
                        </div>
                        <span class="service-show-count">
                            {{ count($reservation->itinerary ?? []) }} destinasi
                        </span>
                    </div>
                    <div class="services-table-scroll">
                        <table class="services-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Jam</th>
                                    <th>Destinasi / Tujuan</th>
                                    <th>Map</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($itineraryStops = $reservation->itinerary ?? [])
                                @if (is_array($itineraryStops))
                                    @forelse($itineraryStops as $stop)
                                        <tr>
                                            <td>
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>
                                                <div class="services-primary-text">{{ $stop['visit_time'] ?? '—' }}</div>
                                            </td>
                                            <td>
                                                <div class="services-primary-text">{{ $stop['name'] ?? 'Destinasi' }}</div>
                                            </td>
                                            <td>
                                                @if (!empty($stop['google_maps_url']))
                                                    <div class="services-primary-text">
                                                        <a href="{{ $stop['google_maps_url'] }}" target="_blank" rel="noopener noreferrer">Buka Google Maps</a>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">
                                                <div class="empty-value">Itinerary tidak tersedia.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                @else
                                    <div class="resv-value" style="white-space:pre-line">{{ $reservation->itinerary ?: '—' }}</div>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </section>
                <section class="service-create-card">
                    @if ($isAdmin && $reservation->status === 'Pending')
                        <div class="services-panel-heading">
                            <div class="service-show-panel-heading">
                                <div class="service-show-panel-icon service-show-blue">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <circle cx="12" cy="8" r="3.5"
                                            stroke="currentColor" stroke-width="1.7" />
                                        <path d="M5 20v-1.5a7 7 0 0 1 14 0V20"
                                            stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                        <path d="m15.5 14.5 1.8 1.8 3.2-3.5"
                                            stroke="currentColor" stroke-width="1.7"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="resv-section-title">Persetujuan Permintaan Layanan</h2>
                                    <p>Setujui hanya setelah memastikan jadwal, driver, kendaraan, dan kontak darurat. SPK akan diterbitkan otomatis.</p>
                                </div>
                            </div>
                            <span class="service-show-count">
                                {{ now()->format('d F Y') }}
                            </span>
                        </div>
                        <form method="POST" action="{{ route('admin.reservations.approve', $reservation) }}">
                            @csrf
                            <div class="service-create-fields">
                                <div class="service-create-field">
                                    <label for="driver_id">Driver <span>*</span></label>
                                    <select id="driver_id" name="driver_id" required>
                                        <option value="">Pilih driver</option>
                                        @foreach ($drivers as $d)
                                            <option value="{{ $d->id }}" @selected(old('driver_id') == $d->id)>
                                                {{ $d->name }}{{ $d->phone ? ' · ' . $d->phone : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('driver_id')
                                        <small class="service-create-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="service-create-field">
                                    <label for="vehicle_id">Kendaraan <span>*</span></label>
                                    <select id="vehicle_id" name="vehicle_id" required>
                                        <option value="">Pilih kendaraan</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') == $vehicle->id)>{{ $vehicle->name }} · {{ $vehicle->plate_number }} ({{ $vehicle->capacity }} seat)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')
                                        <small class="service-create-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="service-create-field service-field-full m-b-3">
                                    <label for="admin_notes">Catatan Layanan</label>
                                    <textarea id="admin_notes" name="admin_notes" rows="3"
                                        placeholder="Permintaan khusus, informasi tamu, atau instruksi tambahan...">{{ old('admin_notes') }}</textarea>
                                    @error('admin_notes')
                                        <small class="service-create-error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="service-create-actions">
                                <button class="service-submit-btn" type="submit">Setujui &amp; Buat SPK</button>
                            </div>
                        </form>
                        <hr style="border:0;border-top:1px solid var(--tms-border);margin:24px 0">
                        <div class="services-panel-heading">
                            <div class="service-show-panel-heading">
                                <div class="service-show-panel-icon service-show-blue">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <circle cx="12" cy="8" r="3.5"
                                            stroke="currentColor" stroke-width="1.7" />
                                        <path d="M5 20v-1.5a7 7 0 0 1 14 0V20"
                                            stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                        <path d="m15.5 14.5 1.8 1.8 3.2-3.5"
                                            stroke="currentColor" stroke-width="1.7"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="resv-section-title">Penolakan Permintaan Layanan</h2>
                                    <p>Setujui hanya setelah memastikan jadwal, driver, kendaraan, dan kontak darurat. SPK akan diterbitkan otomatis.</p>
                                </div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('admin.reservations.reject', $reservation) }}">
                            @csrf
                            <div class="service-create-fields">
                                <div class="service-create-field service-field-full">
                                    <label for="reject_admin_notes">Alasan Penolakan <span>*</span></label>
                                    <textarea id="reject_admin_notes" name="reject_admin_notes" rows="3"
                                        placeholder="Berikan alasan penolakan permintaan layanan" required>{{ old('reject_admin_notes') }}</textarea>
                                    @error('reject_admin_notes')
                                        <small class="service-create-error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="service-create-actions">
                                <button class="service-reject-btn" type="submit">Tolak Permintaan</button>
                            </div>
                        </form>
                    @endif
                    @if (!$isAdmin && $reservation->status === 'Pending')
                        <section class="resv-card">
                            <form method="POST" action="{{ route('reservations.cancel', $reservation) }}">
                                @csrf
                                <button class="resv-btn red" type="submit" onclick="return confirm('Batalkan permintaan ini?')">Batalkan Permintaan</button>
                            </form>
                        </section>
                    @endif
                </section>
                <div class="resv-actions">
                    <a class="service-show-back" href="{{ $isAdmin ? route('admin.reservations.index') : route('reservations.index') }}">
                        ← Kembali ke daftar permintaan
                    </a>
                </div>
            </div>
            <aside class="tms-info-panel" aria-label="Informasi Ringkasan Layanan">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Ringkasan Layanan</h2>
                    <p>Ringkasan daftar dan status layanan transportasi.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <strong>Status</strong>
                        <div class="{{ $statusClass }}">{{ $reservation->status }}</div>
                    </div>
                    @if (count($reservation->guests)< 1)
                        <div class="attention-grid">
                            <div class="attention-svg">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M10.3 4.5 2.8 18a1.5 1.5 0 0 0 1.3 2.2h15.8a1.5 1.5 0 0 0 1.3-2.2L13.7 4.5a1.9 1.9 0 0 0-3.4 0Z"
                                        stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
                                    <path d="M12 9v4.5"
                                        stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                    <circle cx="12" cy="17" r="0.8" fill="currentColor" />
                                </svg>
                            </div>
                            <div class="attention">
                                <span class="label">Daftar Tamu</span>
                                <span class="description">Daftar tamu belum terisi! tambahkan setidaknya satu tamu!</span>
                            </div>
                        </div>
                    @endif
                    @if ($reservation->itinerary == null)
                        <div class="attention-grid">
                            <div class="attention-svg">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M10.3 4.5 2.8 18a1.5 1.5 0 0 0 1.3 2.2h15.8a1.5 1.5 0 0 0 1.3-2.2L13.7 4.5a1.9 1.9 0 0 0-3.4 0Z"
                                        stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
                                    <path d="M12 9v4.5"
                                        stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                    <circle cx="12" cy="17" r="0.8" fill="currentColor" />
                                </svg>
                            </div>
                            <div class="attention">
                                <span class="label">Itinerary</span>
                                <span class="description">tambahkan setidaknya 1 tujuan / destinasi pada itinerary!</span>
                            </div>
                        </div>
                    @endif
                    @if ($reservation->transportService)
                        <div class="tms-info-item">
                            <span class="tms-info-label">SPK aktif</span>
                            <strong>
                                <a href="{{ route('services.show', $reservation->transportService) }}">SPK - {{ $reservation->transportService->service_code }}</a>
                            </strong>
                        </div>
                    @endif
                    @if ($reservation->admin_notes)
                        <div class="tms-info-item">
                            <span class="tms-info-label">Catatan Admin</span>
                            <span class="tms-info-description">{{ $reservation->admin_notes }}</span>
                        </div>
                    @endif
                    @if ($reservation->status == "Pending")
                        <div class="tms-info-item">
                            <span class="tms-info-label">Menunggu persetujuan</span>
                            <span class="tms-info-description">Menunggu untuk diproses / direncanakan</span>
                        </div>
                    @endif
                </div>
            </aside>
        </div>
@endsection
