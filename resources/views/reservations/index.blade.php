@extends('layouts.app')
@section('title', 'Permintaan Transportasi')
@section('content')
    <header>
        <div class="main-header">
            <div class="services-heading">
                <div class="services-eyebrow">
                    <span class="services-eyebrow-dot"></span>
                    LAYANAN TRANSPORTASI
                </div>
                <h1>Daftar Permintaan Layanan Transportasi</h1>
                <p class="resv-muted">Ajukan kebutuhan transportasi dan pantau keputusan admin.</p>
            </div>
            <a href="{{ route('reservations.create')}}" class="services-create-btn">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                <span>Ajukan Permintaan Layanan</span>
            </a>
        </div>
    </header>
    <div class="services-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
                {{-- Filter Panel --}}
                <section class="services-filter-panel">
                    <div class="services-panel-heading">
                        <div class="services-panel-title">
                            <span class="services-filter-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 7h16M7 12h10m-7 5h4" stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" />
                                </svg>
                            </span>
                            <div>
                                <h2>Filter Permintaan Layanan</h2>
                                <p>Temukan permintaan layanan berdasarkan nomor pesanan, pelanggan, pemohon.</p>
                            </div>
                        </div>
                    </div>
                    <form method="GET" class="services-filter-form">
                        <div class="services-filter-field w-100">
                            <label for="service-search">Nomor pesanan / Pelanggan / Pemohonn</label>
                            <div class="services-input-wrap">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor"
                                        stroke-width="1.7" />
                                    <path d="m16 16 4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                </svg>
                                <input type="search" name="q" class="input-text" value="{{ request('q') }}" placeholder="Cari nomor / pelanggan / pemohon">
                            </div>
                        </div>
                        <div class="services-filter-field">
                            <label for="service-status">Status Permintaan</label>
                            <select name="status">
                                <option value="">Semua status</option>
                                @foreach (['Pending', 'Approved', 'Rejected', 'Cancelled'] as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="services-filter-btn">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 7h16M7 12h10m-7 5h4" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" />
                            </svg>
                            Terapkan
                        </button>
                    </form>
                    
                </section>
                <section class="services-table-panel">
                    <div class="services-table-header">
                        <div>
                            <h2>Daftar Permintaan Layanan</h2>
                            <p>Data permintaan layanan yang tersedia</p>
                        </div>
                        <div class="services-total">
                            <span class="services-total-dot"></span>
                            {{ number_format($reservations->total()) }} Permintaan
                        </div>
                    </div>
                    <div class="resv-card">
                        <div class="services-table-scroll">
                            <table class="services-table">
                                <thead>
                                    <tr>
                                        <th>Nomor</th>
                                        <th>Jadwal</th>
                                        <th>Pelanggan</th>
                                        <th>Layanan</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reservations as $r)
                                        <tr>
                                            <td>
                                                <strong>{{ $r->reservation_number }}</strong><br>
                                                <small class="resv-muted">{{ \App\Support\DateFormatter::format($r->created_at, null, 'd M Y H:i') }}</small>
                                            </td>
                                            <td>
                                                {{ \App\Support\DateFormatter::format($r->service_date) }}<br>
                                                <small class="resv-muted">s/d {{ \App\Support\DateFormatter::format($r->service_end_date) }}</small>
                                            </td>
                                            <td>
                                                {{ $r->customer_name }}
                                            </td>
                                            <td>
                                                {{ $r->serviceType?->name ?? '—' }}
                                            </td>
                                            <td>
                                                <span class="resv-status {{ $r->status }}">{{ $r->status }}</span>
                                            </td>
                                            <td class="services-action-cell">
                                                <a href="{{ route('reservations.show', $r) }}" class="services-detail-btn"
                                                    aria-label="Detail">
                                                    Detail
                                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </a>
                                            </td>
                                            {{-- <td>
                                                <a class="resv-link" href="{{ route('reservations.show', $r) }}">Detail →</a>
                                            </td> --}}
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="empty-value">Belum ada permintaan yang anda buat.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div style="margin-top:18px">{{ $reservations->links() }}</div>
                    </div>
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
                        <span class="tms-info-label">Menunggu Persetujuan</span>
                        <strong class="tms-info-value">{{ number_format($pendingReservation->count()) }}</strong>
                        <span class="tms-info-description">Pending</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Disetujui</span>
                        <strong class="tms-info-value">{{ number_format($approvedReservation->count()) }}</strong>
                        <span class="tms-info-description">Approved</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Ditolak</span>
                        <strong class="tms-info-value">{{ number_format($rejectedReservation->count()) }}</strong>
                        <span class="tms-info-description">Rejected</span>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection
