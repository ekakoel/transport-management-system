@extends('layouts.app')
@section('title', 'Review Permintaan')
@section('content')
    <header>
        <div class="main-header">
            <div class="services-heading">
                <div class="services-eyebrow">
                    <span class="services-eyebrow-dot"></span>
                    PERMINTAAN LAYANAN TRANSPORTASI
                </div>
                <h1>Review Permintaan Transportasi</h1>
                <p class="resv-muted">Tinjau permintaan Reservation. Persetujuan membuat Transport Service dan SPK.</p>
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
                                        <th>Pemohon</th>
                                        <th>Jadwal</th>
                                        <th>Pelanggan</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reservations as $r)
                                        <tr>
                                            <td><strong>{{ $r->reservation_number }}</strong><br><small
                                                    class="resv-muted">{{ $r->serviceType?->name }}</small></td>
                                            <td>{{ $r->requester?->name }}<br><small
                                                    class="resv-muted">{{ $r->requester?->email }}</small></td>
                                            <td>{{ \App\Support\DateFormatter::format($r->service_date) }}<br><small class="resv-muted">s/d
                                                    {{ \App\Support\DateFormatter::format($r->service_end_date) }}</small></td>
                                            <td>{{ $r->customer_name }}</td>
                                            <td><span class="resv-status {{ $r->status }}">{{ $r->status }}</span></td>
                                            {{-- <td><a class="resv-btn" href="{{ route('admin.reservations.show', $r) }}">Tinjau</a></td> --}}
                                            <td class="services-action-cell">
                                                <a href="{{ route('admin.reservations.show', $r) }}" class="services-detail-btn"
                                                    aria-label="Detail">
                                                    Detail
                                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty<tr>
                                            <td colspan="6" class="resv-muted">Tidak ada permintaan yang cocok.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div style="margin-top:18px">{{ $reservations->links() }}</div>
                    </div>
                </section>
                @if ($layananReservationsExpired->total() > 0)
                    <section class="services-table-panel">
                        <div class="services-table-header">
                            <div>
                                <h2>Daftar Permintaan Layanan Diasrsipkan</h2>
                                <p>Data permintaan layanan yang telah kedaluarsa</p>
                            </div>
                            <div class="services-total">
                                <span class="services-total-dot"></span>
                                {{ number_format($layananReservationsExpired->total()) }} Permintaan
                            </div>
                        </div>
                        <div class="resv-card">
                            <div class="services-table-scroll">
                                <table class="services-table">
                                    <thead>
                                        <tr>
                                            <th>Nomor</th>
                                            <th>Pemohon</th>
                                            <th>Jadwal</th>
                                            <th>Pelanggan</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($layananReservationsExpired as $x)
                                            <tr>
                                                <td><strong>{{ $x->reservation_number }}</strong><br><small
                                                        class="resv-muted">{{ $x->serviceType?->name }}</small></td>
                                                <td>{{ $x->requester?->name }}<br><small
                                                        class="resv-muted">{{ $x->requester?->email }}</small></td>
                                                <td>{{ \App\Support\DateFormatter::format($x->service_date) }}<br><small class="resv-muted">s/d
                                                        {{ \App\Support\DateFormatter::format($x->service_end_date) }}</small></td>
                                                <td>{{ $x->customer_name }}</td>
                                                <td><span class="resv-status {{ strtolower($x->status ?? '') }}">{{ $x->status }}</span></td>
                                                <td class="services-action-cell">
                                                    <a href="{{ route('admin.reservations.show', $x) }}" class="services-detail-btn"
                                                        aria-label="Detail">
                                                        Detail
                                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                            <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty<tr>
                                                <td colspan="6" class="resv-muted">Tidak ada permintaan yang cocok.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div style="margin-top:18px">{{ $layananReservationsExpired->links() }}</div>
                        </div>
                        
                    </section>
                @endif
            </div>
            <aside class="tms-info-panel" aria-label="Informasi Ringkasan Layanan">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Ringkasan Layanan</h2>
                    <p>Ringkasan daftar dan status layanan transportasi.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Permintaan Layanan saat ini</span>
                        <strong class="tms-info-value">{{ number_format($todayActiveReservations->count()) }}</strong>
                        <span class="tms-info-description">Jumlah permintaan layanan hari ini.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Permintaan Layanan mendatang</span>
                        <strong class="tms-info-value">{{ number_format($layananReservationsAkanDatang->count()) }}</strong>
                        <span class="tms-info-description">Status approved</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Permintaan Layanan menunggu diproses</span>
                        <strong
                            class="tms-info-value">{{ number_format($layananReservationsMenungguDiproses->count()) }}</strong>
                        <span class="tms-info-description">Status Pending.</span>
                    </div>
                </div>
            </aside>
        </div>
@endsection
