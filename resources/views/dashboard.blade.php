@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="dashboard-page">
        <div class="tms-two-column">
            <div class="tms-main-column">

        <div class="dashboard-welcome">
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
                    <path d="M12 5v14M5 12h14" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round"/>
                </svg>
                Buat layanan / SPK
            </a>
        </div>

        <div class="dashboard-date">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="3" y="5" width="18" height="16" rx="3"
                    stroke="currentColor" stroke-width="1.7"/>
                <path d="M16 3v4M8 3v4M3 10h18"
                    stroke="currentColor" stroke-width="1.7"
                    stroke-linecap="round"/>
            </svg>
            <span>{{ now()->translatedFormat('l, d F Y') }}</span>
        </div>

        <div class="dashboard-stats">

            <article class="stat-card stat-blue">
                <div class="stat-card-top">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="16" rx="3"
                                stroke="currentColor" stroke-width="1.7"/>
                            <path d="M16 3v4M8 3v4M3 10h18"
                                stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round"/>
                        </svg>
                    </div>
                    <span class="stat-label">HARI INI</span>
                </div>
                <div class="stat-value">{{ $todayCount }}</div>
                <div class="stat-title">Layanan hari ini</div>
                <div class="stat-foot">Total layanan berdasarkan tanggal hari ini</div>
            </article>

            <article class="stat-card stat-green">
                <div class="stat-card-top">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"
                                stroke="currentColor" stroke-width="1.7"/>
                            <path d="m8 12 2.5 2.5L16 9"
                                stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="stat-label">ACTIVE</span>
                </div>
                <div class="stat-value">{{ $activeCount }}</div>
                <div class="stat-title">SPK aktif</div>
                <div class="stat-foot">Surat perintah kerja yang masih aktif</div>
            </article>

            <article class="stat-card stat-orange">
                <div class="stat-card-top">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"
                                stroke="currentColor" stroke-width="1.7"/>
                            <path d="M12 7v5l3 2"
                                stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="stat-label">EXPIRED</span>
                </div>
                <div class="stat-value">{{ $expiredCount }}</div>
                <div class="stat-title">SPK expired</div>
                <div class="stat-foot">SPK yang telah melewati masa berlaku</div>
            </article>

            <article class="stat-card stat-purple">
                <div class="stat-card-top">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="9" cy="8" r="3.5"
                                stroke="currentColor" stroke-width="1.7"/>
                            <path d="M2.5 20v-1.5a6.5 6.5 0 0 1 13 0V20M16 5a3.5 3.5 0 0 1 0 6.8M18 14a5 5 0 0 1 3.5 4.8V20"
                                stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round"/>
                        </svg>
                    </div>
                    <span class="stat-label">TEAM</span>
                </div>
                <div class="stat-value">{{ $driverCount }}</div>
                <div class="stat-title">Driver aktif</div>
                <div class="stat-foot">Jumlah driver aktif pada sistem</div>
            </article>

        </div>

        <section class="dashboard-panel">
            <div class="panel-heading">
                <div>
                    <div class="panel-title">Layanan terbaru</div>
                    <div class="panel-subtitle">
                        Daftar layanan transportasi yang tercatat di sistem
                    </div>
                </div>

                <a class="panel-link" href="{{ route('services.index') }}">
                    Lihat semua
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12h14m-6-6 6 6-6 6"
                            stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round"/>
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
                            <th>DRIVER</th>
                            <th>KENDARAAN</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $s)
                            <tr>
                                <td>
                                    <a class="service-code"
                                        href="{{ route('services.show', $s) }}">
                                        {{ $s->service_code }}
                                    </a>
                                    <div class="service-spk">
                                        {{ $s->spk?->spk_number ?? 'SPK belum tersedia' }}
                                    </div>
                                </td>
                                <td class="table-date">
                                    {{ $s->service_date->format('d M Y') }}
                                </td>
                                <td class="table-customer">
                                    {{ $s->customer_name }}
                                </td>
                                <td>{{ $s->driver?->name ?? '—' }}</td>
                                <td>
                                    @if ($s->vehicle?->plate_number)
                                        <span class="vehicle-plate">
                                            {{ $s->vehicle->plate_number }}
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
                                <td colspan="6">
                                    <div class="dashboard-empty">
                                        <div class="empty-icon">
                                            <svg viewBox="0 0 24 24" fill="none"
                                                aria-hidden="true">
                                                <rect x="3" y="4" width="18" height="17"
                                                    rx="3" stroke="currentColor"
                                                    stroke-width="1.6"/>
                                                <path d="M7 9h10M7 13h6"
                                                    stroke="currentColor"
                                                    stroke-width="1.6"
                                                    stroke-linecap="round"/>
                                            </svg>
                                        </div>
                                        <strong>Belum ada layanan</strong>
                                        <span>Layanan terbaru akan muncul di sini setelah dibuat.</span>
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
                Menampilkan {{ $services->count() }} layanan terbaru
            </div>
        </section>

        <footer class="dashboard-footer">
            <span>Bali Kami TMS</span>
            <span>Transport Management System</span>
        </footer>

            </div>

            <aside class="tms-info-panel" aria-label="Informasi Ringkasan Operasional">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Ringkasan Operasional</h2>
                    <p>Pantauan cepat kondisi operasional saat ini.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Layanan hari ini</span>
                        <strong class="tms-info-value">{{ number_format($todayCount) }}</strong>
                        <span class="tms-info-description">Berdasarkan tanggal layanan hari ini.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">SPK aktif</span>
                        <strong class="tms-info-value">{{ number_format($activeCount) }}</strong>
                        <span class="tms-info-description">Status Published atau In Progress.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">SPK expired</span>
                        <strong class="tms-info-value">{{ number_format($expiredCount) }}</strong>
                        <span class="tms-info-description">Layanan dengan status Expired.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Driver aktif</span>
                        <strong class="tms-info-value">{{ number_format($driverCount) }}</strong>
                        <span class="tms-info-description">Driver yang dapat dipilih pada operasional.</span>
                    </div>
                </div>
                <div class="tms-info-panel-footer"><a href="{{ route('services.index') }}">Buka daftar layanan →</a></div>
            </aside>
        </div>
    </div>
@endsection
