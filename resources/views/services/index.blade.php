@extends('layouts.app')

@section('title', 'Transport Services')

@section('content')
    <div class="services-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
        {{-- Page Header --}}
        <div class="services-header">
            <div class="services-heading">
                <div class="services-eyebrow">
                    <span class="services-eyebrow-dot"></span>
                    TRANSPORT OPERATIONS
                </div>
                <h1>Transport Services / SPK</h1>
                <p>Kelola layanan transportasi, pantau penugasan driver, dan periksa status operasional.</p>
            </div>

            <a href="{{ route('services.create') }}" class="services-create-btn">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round"/>
                </svg>
                <span>Buat SPK</span>
            </a>
        </div>

        {{-- Filter Panel --}}
        <section class="services-filter-panel">
            <div class="services-panel-heading">
                <div class="services-panel-title">
                    <span class="services-filter-icon">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 7h16M7 12h10m-7 5h4" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <div>
                        <h2>Filter Layanan</h2>
                        <p>Temukan SPK berdasarkan kode, tanggal, atau status.</p>
                    </div>
                </div>

                @if (request()->filled('q') || request()->filled('date') || request()->filled('status'))
                    <a href="{{ route('services.index') }}" class="services-clear-link">
                        Reset filter
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('services.index') }}" class="services-filter-form">
                <div class="services-filter-field w-100">
                    <label for="service-search">Kode SPK / Pelanggan</label>
                    <div class="services-input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor"
                                stroke-width="1.7"/>
                            <path d="m16 16 4 4" stroke="currentColor"
                                stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                        <input id="service-search" type="search" name="q" class="input-text"
                            value="{{ request('q') }}"
                            placeholder="Cari nomor order atau nama pelanggan...">
                    </div>
                </div>

                <div class="services-filter-field">
                    <label for="service-date">Tanggal Layanan</label>
                    <input id="service-date" type="date" name="date"
                        value="{{ request('date') }}">
                </div>

                <div class="services-filter-field">
                    <label for="service-status">Status Layanan</label>
                    <select id="service-status" name="status">
                        <option value="">Semua status</option>
                        @foreach (['Draft', 'Published', 'In Progress', 'Completed', 'Cancelled', 'Expired'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="services-filter-actions">
                    <button type="submit" class="services-filter-btn">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 7h16M7 12h10m-7 5h4" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        Terapkan
                    </button>
                    <a href="{{ route('services.index') }}" class="services-reset-btn">
                        Reset
                    </a>
                </div>
            </form>
        </section>

        {{-- Services Table --}}
        <section class="services-table-panel">
            <div class="services-table-header">
                <div>
                    <h2>Daftar Layanan</h2>
                    <p>Data SPK dan penugasan transportasi.</p>
                </div>
                <div class="services-total">
                    <span class="services-total-dot"></span>
                    {{ number_format($services->total()) }} layanan
                </div>
            </div>

            <div class="services-table-scroll">
                <table class="services-table">
                    <thead>
                        <tr>
                            <th>Nomor Order / SPK</th>
                            <th>Tanggal Layanan</th>
                            <th>Pelanggan</th>
                            <th>Driver / Kendaraan</th>
                            <th>Status</th>
                            <th class="services-action-heading">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($services as $s)
                            @php
                                $statusClass = match ($s->status) {
                                    'Draft' => 'draft',
                                    'Published' => 'published',
                                    'In Progress' => 'progress',
                                    'Completed' => 'completed',
                                    'Cancelled' => 'cancelled',
                                    'Expired' => 'expired',
                                    default => 'default',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="services-code">{{ $s->service_code }}</div>
                                    <div class="services-subtext">
                                        {{ $s->spk?->spk_number ?: 'SPK belum tersedia' }}
                                    </div>
                                </td>

                                <td>
                                    <div class="services-date">
                                        {{ $s->service_date->format('d M Y') }}
                                    </div>
                                    @if ($s->service_end_date->format('Y-m-d') !== $s->service_date->format('Y-m-d'))
                                        <div class="services-subtext">
                                            s/d {{ $s->service_end_date->format('d M Y') }}
                                        </div>
                                    @else
                                        <div class="services-subtext">Satu hari</div>
                                    @endif
                                </td>

                                <td>
                                    <div class="services-primary-text">{{ $s->customer_name }}</div>
                                    <div class="services-subtext">
                                        {{ $s->serviceType?->name ?? 'Tipe layanan belum ditentukan' }}
                                    </div>
                                </td>

                                <td>
                                    <div class="services-primary-text">
                                        {{ $s->driver?->name ?? 'Belum ditugaskan' }}
                                    </div>
                                    <div class="services-subtext">
                                        {{ $s->vehicle?->plate_number ?? 'Kendaraan belum ditentukan' }}
                                    </div>
                                </td>

                                <td>
                                    <span class="services-status services-status-{{ $statusClass }}">
                                        <span class="services-status-dot"></span>
                                        {{ $s->status }}
                                    </span>
                                </td>

                                <td class="services-action-cell">
                                    <a href="{{ route('services.show', $s) }}"
                                        class="services-detail-btn" aria-label="Detail {{ $s->service_code }}">
                                        Detail
                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor"
                                                stroke-width="1.8" stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="services-empty-state">
                                        <span class="services-empty-icon">
                                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <rect x="4" y="3" width="16" height="18" rx="2"
                                                    stroke="currentColor" stroke-width="1.6"/>
                                                <path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor"
                                                    stroke-width="1.6" stroke-linecap="round"/>
                                            </svg>
                                        </span>
                                        <h3>Tidak ada layanan ditemukan</h3>
                                        <p>Ubah filter pencarian atau buat SPK baru untuk memulai.</p>
                                        <a href="{{ route('services.create') }}" class="services-empty-link">
                                            + Buat SPK baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($services->hasPages())
                <div class="services-pagination">
                    <div class="services-pagination-info">
                        Menampilkan {{ $services->firstItem() }}–{{ $services->lastItem() }}
                        dari {{ number_format($services->total()) }} layanan
                    </div>
                    <div class="services-pagination-links">
                        {{ $services->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        </section>
            </div>

            <aside class="tms-info-panel" aria-label="Informasi Ringkasan Layanan">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Ringkasan Layanan</h2>
                    <p>Ringkasan daftar dan status layanan transportasi.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Hasil saat ini</span>
                        <strong class="tms-info-value">{{ number_format($services->total()) }}</strong>
                        <span class="tms-info-description">Jumlah layanan sesuai filter aktif.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Layanan mendatang</span>
                        <strong class="tms-info-value">{{ number_format($upcomingServiceCount) }}</strong>
                        <span class="tms-info-description">Belum selesai, dibatalkan, atau expired.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">SPK aktif</span>
                        <strong class="tms-info-value">{{ number_format($serviceStatusCounts['Published'] ?? 0) }}</strong>
                        <span class="tms-info-description">Status Published.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Dalam proses</span>
                        <strong class="tms-info-value">{{ number_format($serviceStatusCounts['In Progress'] ?? 0) }}</strong>
                        <span class="tms-info-description">Status In Progress.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Selesai</span>
                        <strong class="tms-info-value">{{ number_format($serviceStatusCounts['Completed'] ?? 0) }}</strong>
                        <span class="tms-info-description">Status Completed.</span>
                    </div>
                </div>
                <div class="tms-info-panel-footer"><a href="{{ route('services.create') }}">+ Buat layanan / SPK</a></div>
            </aside>
        </div>
    </div>
@endsection
