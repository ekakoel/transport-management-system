@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <header>
        <div class="main-header">
            <div>
                <div class="eyebrow">
                    <span class="status-dot"></span>
                    OPERATIONS OVERVIEW
                </div>
                <h1>Dashboard Operasional</h1>
                <p>Pantau aktivitas transportasi dan status layanan Anda hari ini.</p>
            </div>

            <a class="btn dashboard-create-btn" href="{{ route('services.create') }}">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                Buat layanan / SPK
            </a>
        </div>
        <div class="dashboard-date">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="3" y="5" width="18" height="16" rx="3" stroke="currentColor"
                    stroke-width="1.7" />
                <path d="M16 3v4M8 3v4M3 10h18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
            </svg>
            <span>{{ \App\Support\DateFormatter::format(now()) }}</span>
        </div>
    </header>
    <div class="dashboard-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
                <div class="dashboard-stats">
                    <article class="stat-card stat-blue">
                        <div class="stat-card-top">
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="5" y="4" width="14" height="17" rx="2.5" stroke="currentColor"
                                        stroke-width="1.7" />
                                    <path d="M9 3h6v3H9z" stroke="currentColor" stroke-width="1.7"
                                        stroke-linejoin="round" />
                                    <path d="m8.5 13 2 2 4-4" stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span class="stat-label">RESERVASI</span>
                        </div>
                        <div class="stat-value">{{ number_format($todayReservationCount) }}</div>
                        <div class="stat-title">Reservasi layanan hari ini</div>
                        <div class="stat-foot">Pending dan Approved yang berlangsung hari ini.</div>
                    </article>

                    <article class="stat-card stat-green">
                        <div class="stat-card-top">
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7" />
                                    <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span class="stat-label">LAYANAN</span>
                        </div>
                        <div class="stat-value">{{ number_format($todayActiveServiceCount) }}</div>
                        <div class="stat-title">Transportasi aktif hari ini</div>
                        <div class="stat-foot">Published atau In Progress yang berlangsung hari ini.</div>
                    </article>

                    <article class="stat-card stat-orange">
                        <div class="stat-card-top">
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m5 11 2-5h10l2 5M4 11h16v7H4z" stroke="currentColor"
                                        stroke-width="1.7" stroke-linejoin="round" />
                                    <path d="M7 18v2m10-2v2M4 14h3m10 0h3" stroke="currentColor"
                                        stroke-width="1.7" stroke-linecap="round" />
                                    <circle cx="7.5" cy="14.5" r=".8" fill="currentColor" />
                                    <circle cx="16.5" cy="14.5" r=".8" fill="currentColor" />
                                </svg>
                            </div>
                            <span class="stat-label">KENDARAAN</span>
                        </div>
                        <div class="stat-value">{{ number_format($activeVehicleCount) }}</div>
                        <div class="stat-title">Kendaraan aktif</div>
                        <div class="stat-foot">Kendaraan yang sedang digunakan pada layanan operasional hari ini.</div>
                    </article>

                    <article class="stat-card stat-purple">
                        <div class="stat-card-top">
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="9" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7" />
                                    <path
                                        d="M2.5 20v-1.5a6.5 6.5 0 0 1 13 0V20M16 5a3.5 3.5 0 0 1 0 6.8M18 14a5 5 0 0 1 3.5 4.8V20"
                                        stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                </svg>
                            </div>
                            <span class="stat-label">DRIVER</span>
                        </div>
                        <div class="stat-value">{{ number_format($activeDriverCount) }}</div>
                        <div class="stat-title">Driver aktif</div>
                        <div class="stat-foot">Driver yang sedang mengerjakan layanan operasional hari ini.</div>
                    </article>
                </div>

                

                <section class="dashboard-panel mb-3">
                    <div class="panel-heading">
                        <div>
                            <div class="panel-title">Layanan aktif &amp; mendatang</div>
                            <div class="panel-subtitle">
                                Layanan yang belum terlewat dan masih harus dikerjakan
                            </div>
                        </div>

                        <a class="panel-link" href="{{ route('services.index') }}">
                            Lihat semua
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    </div>

                    <div class="dashboard-table-wrap">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>KODE / SPK</th>
                                    <th>TANGGAL</th>
                                    <th>PELANGGAN</th>
                                    <th>TAMU</th>
                                    <th>DRIVER</th>
                                    <th>KENDARAAN</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $s)
                                    <tr>
                                        <td>
                                            <a class="service-code" href="{{ route('services.show', $s) }}">
                                                {{ $s->service_code }}
                                            </a>
                                            <div class="service-spk">
                                                {{ $s->spk?->spk_number ?? 'SPK belum tersedia' }}
                                            </div>
                                        </td>
                                        <td class="table-date">
                                            {{ \App\Support\DateFormatter::format($s->service_date) }}
                                        </td>
                                        <td class="table-customer">
                                            {{ $s->customer_name }}
                                        </td>
                                        <td>{{ number_format($s->reservation?->guest_count ?? 1) }} orang</td>
                                        <td>{{ $s->driver?->name ?? '—' }}</td>
                                        <td>
                                            @if ($s->plate_number)
                                                <span class="vehicle-plate">
                                                    {{ $s->plate_number }}
                                                </span>
                                            @else
                                                <span class="table-empty">Belum ditentukan</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="service-status"
                                                data-status="{{ strtolower($s->status ?? '') }}">
                                                <span class="status-dot"></span>
                                                {{ $s->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="dashboard-empty">
                                                <div class="empty-icon">
                                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <rect x="3" y="4" width="18" height="17" rx="3"
                                                            stroke="currentColor" stroke-width="1.6" />
                                                        <path d="M7 9h10M7 13h6" stroke="currentColor" stroke-width="1.6"
                                                            stroke-linecap="round" />
                                                    </svg>
                                                </div>
                                                <strong>Belum ada layanan aktif atau mendatang</strong>
                                                <span>Layanan yang masih harus dikerjakan akan muncul di sini.</span>
                                                <a href="{{ route('services.create') }}">
                                                    Buat layanan pertama
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="panel-footer">
                        Menampilkan {{ $services->count() }} layanan aktif dan mendatang
                    </div>
                </section>
                <section class="dashboard-panel dashboard-live-panel" aria-labelledby="liveCheckInsTitle">
                    <div class="panel-heading">
                        <div>
                            <div class="panel-title" id="liveCheckInsTitle">Check In driver terbaru</div>
                            <div class="panel-subtitle">Aktivitas Check In dari SPK yang diperbarui otomatis.</div>
                        </div>
                        <span class="dashboard-live-status" id="liveCheckInsStatus" role="status" aria-live="polite">
                            Live · memperbarui setiap 15 detik
                        </span>
                    </div>

                    <div class="dashboard-live-feed" id="liveCheckInsFeed"
                        data-url="{{ route('dashboard.live-check-ins') }}">
                        @forelse ($latestDriverCheckIns as $checkIn)
                            <article class="dashboard-live-item">
                                <div class="dashboard-live-indicator" aria-hidden="true"></div>
                                <div class="dashboard-live-main">
                                    <div class="dashboard-live-heading">
                                        @if ($checkIn['service_url'])
                                            <a class="service-code" href="{{ $checkIn['service_url'] }}">
                                                {{ $checkIn['service_code'] }}
                                            </a>
                                        @else
                                            <strong>{{ $checkIn['service_code'] }}</strong>
                                        @endif
                                        <span class="dashboard-live-spk">{{ $checkIn['spk_number'] }}</span>
                                    </div>
                                    <div class="dashboard-live-destination">
                                        Check In di <strong>{{ $checkIn['destination'] }}</strong>
                                    </div>
                                    <div class="dashboard-live-meta">
                                        <span>Driver: {{ $checkIn['driver'] }}</span>
                                        @if ($checkIn['vehicle'])
                                            <span>Kendaraan: {{ $checkIn['vehicle'] }}</span>
                                        @endif
                                        @if ($checkIn['plate_number'])
                                            <span>{{ $checkIn['plate_number'] }}</span>
                                        @endif
                                    </div>
                                    @if ($checkIn['note'])
                                        <p class="dashboard-live-note">{{ $checkIn['note'] }}</p>
                                    @endif
                                </div>
                                <time class="dashboard-live-time">{{ $checkIn['checked_in_at'] }}</time>
                            </article>
                        @empty
                            <div class="dashboard-live-empty">Belum ada aktivitas Check In driver.</div>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="tms-info-panel" aria-label="Informasi Ringkasan Operasional">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Ringkasan Operasional</h2>
                    <p>Pantauan cepat kondisi operasional saat ini.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Reservasi menunggu review</span>
                        <strong class="tms-info-value">{{ number_format($pendingReservationReviewCount) }}</strong>
                        <span class="tms-info-description">Semua reservasi berstatus Pending yang belum ditinjau.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Reservasi menunggu persetujuan</span>
                        <strong class="tms-info-value">{{ number_format($todayPendingReservationCount) }}</strong>
                        <span class="tms-info-description">Reservasi hari ini yang masih berstatus Pending.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Layanan sedang berjalan</span>
                        <strong class="tms-info-value">{{ number_format($todayInProgressServiceCount) }}</strong>
                        <span class="tms-info-description">Layanan hari ini dengan status In Progress.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Penugasan belum lengkap hari ini</span>
                        <strong class="tms-info-value">{{ number_format($incompleteTodayAssignmentCount) }}</strong>
                        <span class="tms-info-description">SPK aktif hari ini yang belum memiliki driver, kendaraan, atau nomor polisi.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Kendaraan ditugaskan</span>
                        <strong class="tms-info-value">{{ number_format($activeVehicleCount) }}</strong>
                        <span class="tms-info-description">Kendaraan yang dipakai pada layanan aktif hari ini.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Driver bertugas</span>
                        <strong class="tms-info-value">{{ number_format($activeDriverCount) }}</strong>
                        <span class="tms-info-description">Driver yang ditugaskan pada layanan aktif hari ini.</span>
                    </div>
                </div>
                <div class="tms-info-panel-footer">
                    <a href="{{ route('admin.reservations.index') }}">Buka reservasi layanan →</a>
                    <span aria-hidden="true"> · </span>
                    <a href="{{ route('services.index') }}">Daftar layanan →</a>
                </div>
            </aside>
        </div>
    </div>
    <script>
        (() => {
            const feed = document.getElementById('liveCheckInsFeed');
            const status = document.getElementById('liveCheckInsStatus');

            if (!feed || !status) return;

            const renderCheckIns = (checkIns) => {
                feed.replaceChildren();

                if (checkIns.length === 0) {
                    const empty = document.createElement('div');
                    empty.className = 'dashboard-live-empty';
                    empty.textContent = 'Belum ada aktivitas Check In driver.';
                    feed.appendChild(empty);
                    return;
                }

                checkIns.forEach((checkIn) => {
                    const item = document.createElement('article');
                    item.className = 'dashboard-live-item';

                    const indicator = document.createElement('div');
                    indicator.className = 'dashboard-live-indicator';
                    indicator.setAttribute('aria-hidden', 'true');
                    item.appendChild(indicator);

                    const main = document.createElement('div');
                    main.className = 'dashboard-live-main';

                    const heading = document.createElement('div');
                    heading.className = 'dashboard-live-heading';
                    if (checkIn.service_url) {
                        const serviceLink = document.createElement('a');
                        serviceLink.className = 'service-code';
                        serviceLink.href = checkIn.service_url;
                        serviceLink.textContent = checkIn.service_code;
                        heading.appendChild(serviceLink);
                    } else {
                        const serviceName = document.createElement('strong');
                        serviceName.textContent = checkIn.service_code;
                        heading.appendChild(serviceName);
                    }

                    const spk = document.createElement('span');
                    spk.className = 'dashboard-live-spk';
                    spk.textContent = checkIn.spk_number;
                    heading.appendChild(spk);
                    main.appendChild(heading);

                    const destination = document.createElement('div');
                    destination.className = 'dashboard-live-destination';
                    destination.append('Check In di ');
                    const destinationName = document.createElement('strong');
                    destinationName.textContent = checkIn.destination;
                    destination.appendChild(destinationName);
                    main.appendChild(destination);

                    const meta = document.createElement('div');
                    meta.className = 'dashboard-live-meta';
                    [
                        `Driver: ${checkIn.driver}`,
                        checkIn.vehicle ? `Kendaraan: ${checkIn.vehicle}` : null,
                        checkIn.plate_number || null
                    ].filter(Boolean).forEach((value) => {
                        const detail = document.createElement('span');
                        detail.textContent = value;
                        meta.appendChild(detail);
                    });
                    main.appendChild(meta);

                    if (checkIn.note) {
                        const note = document.createElement('p');
                        note.className = 'dashboard-live-note';
                        note.textContent = checkIn.note;
                        main.appendChild(note);
                    }

                    item.appendChild(main);
                    const timestamp = document.createElement('time');
                    timestamp.className = 'dashboard-live-time';
                    timestamp.textContent = checkIn.checked_in_at;
                    item.appendChild(timestamp);
                    feed.appendChild(item);
                });
            };

            const refreshCheckIns = async () => {
                try {
                    const response = await fetch(feed.dataset.url, {
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        throw new Error(`Reservasi gagal (${response.status}).`);
                    }

                    const result = await response.json();
                    if (!Array.isArray(result.checkIns)) {
                        throw new Error('Format data Check In tidak valid.');
                    }

                    renderCheckIns(result.checkIns);
                    const updatedAt = new Intl.DateTimeFormat('id-ID', {
                        timeZone: 'Asia/Makassar',
                        hour: '2-digit',
                        minute: '2-digit'
                    }).format(new Date());
                    status.textContent = `Live ${updatedAt} WITA`;
                } catch (error) {
                    status.textContent = error instanceof Error
                        ? `Gagal memperbarui aktivitas Check In: ${error.message}`
                        : 'Gagal memperbarui aktivitas Check In. Data sebelumnya tetap ditampilkan.';
                }
            };

            window.setInterval(refreshCheckIns, 15000);
        })();
    </script>
@endsection
