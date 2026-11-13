@extends('layouts.app')
@section('title', 'Reservasi Layanan')
@section('content')
    <header>
        <div class="main-header">
            <div class="services-heading">
                <div class="services-eyebrow">
                    <span class="services-eyebrow-dot"></span>
                    RESERVASI LAYANAN
                </div>
                <h1>Daftar Reservasi Layanan</h1>
                <p class="resv-muted">Tinjau reservasi dari pemohon. Persetujuan membuat Layanan Transportasi dan SPK.</p>
            </div>
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
                                <h2>Filter Reservasi Layanan</h2>
                                <p>Temukan reservasi layanan berdasarkan nomor pesanan, agen, atau pemohon.</p>
                            </div>
                        </div>
                    </div>
                    <form method="GET" class="services-filter-form">
                        <div class="services-filter-field w-100">
                            <label for="service-search">Nomor pesanan / Agen / Reservasi</label>
                            <div class="services-input-wrap">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor"
                                        stroke-width="1.7" />
                                    <path d="m16 16 4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                </svg>
                                <input type="search" name="q" class="input-text" value="{{ request('q') }}" placeholder="Cari nomor / agen / pemohon">
                            </div>
                        </div>
                        <div class="services-filter-field">
                            <label for="service-status">Status Reservasi</label>
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
                            <h2>Daftar Reservasi Layanan</h2>
                            <p>Data reservasi layanan yang tersedia</p>
                        </div>
                        <div class="services-total">
                            <span class="services-total-dot"></span>
                            {{ number_format($reservations->total()) }} Reservasi
                        </div>
                    </div>
                    <div class="resv-card">
                        <div class="services-table-scroll">
                            <table class="services-table">
                                <thead>
                                    <tr>
                                        <th>Nomor</th>
                                        <th>Reservasi</th>
                                        <th>Jadwal</th>
                                        <th>Agen</th>
                                        <th>Jumlah Tamu</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reservations as $r)
                                        <tr>
                                            <td><strong>{{ $r->reservation_number }}</strong><br>
                                                @if ($r->order_number)
                                                    <small class="resv-muted">Order: {{ $r->order_number }}</small><br>
                                                @endif
                                                <small
                                                    class="resv-muted">{{ $r->serviceType?->name }}</small></td>
                                            <td>{{ $r->requester?->name }}<br><small
                                                    class="resv-muted">{{ $r->requester?->email }}{{ $r->requester?->phone ? ' · ' . $r->requester->phone : '' }}</small></td>
                                            <td>{{ \App\Support\DateFormatter::format($r->service_date) }}<br><small class="resv-muted">s/d
                                                    {{ \App\Support\DateFormatter::format($r->service_end_date) }}</small></td>
                                            <td>{{ $r->customer_name }}</td>
                                            <td>{{ number_format($r->guest_count) }} orang</td>
                                            <td><span class="resv-status {{ $r->status }}">{{ $r->status }}</span></td>
                                            <td class="services-action-cell">
                                                <div class="services-action-group">
                                                    <a href="{{ route('admin.reservations.show', $r) }}" class="services-detail-btn"
                                                        aria-label="Detail">
                                                        Detail
                                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                            <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty<tr>
                                            <td colspan="7" class="resv-muted">Tidak ada reservasi layanan yang tersedia</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="tms-pagination-shell">{{ $reservations->links() }}</div>
                    </div>
                </section>
            </div>
            <aside class="tms-info-panel" aria-label="Informasi Ringkasan Reservasi">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Ringkasan Reservasi</h2>
                    <p>Ringkasan daftar dan status reservasi layanan.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Reservasi Layanan saat ini</span>
                        <strong class="tms-info-value">{{ number_format($todayActiveReservations->count()) }}</strong>
                        <span class="tms-info-description">Jumlah reservasi layanan hari ini.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Reservasi Layanan mendatang</span>
                        <strong class="tms-info-value">{{ number_format($layananReservationsAkanDatang->count()) }}</strong>
                        <span class="tms-info-description">Status approved</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Reservasi Layanan menunggu diproses</span>
                        <strong
                            class="tms-info-value">{{ number_format($layananReservationsMenungguDiproses->count()) }}</strong>
                        <span class="tms-info-description">Status Pending.</span>
                    </div>
                </div>
            </aside>
        </div>
@endsection
