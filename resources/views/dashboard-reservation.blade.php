@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <header>
        <div class="main-header">
            <div>
                <div class="eyebrow">
                    <span class="status-dot"></span>
                    RESERVATION WORKSPACE
                </div>
                <h1>Dashboard Reservasi Layanan</h1>
                <p>Pantau semua reservasi layanan dari semua pemohon beserta status persetujuannya.</p>
            </div>

            <a class="btn dashboard-create-btn" href="{{ route('reservations.create') }}">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                Ajukan Reservasi Layanan
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
                    <article class="stat-card stat-orange">
                        <div class="stat-card-top">
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7" />
                                    <path d="M12 7v5l3.5 2" stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span class="stat-label">MENUNGGU REVIEW</span>
                        </div>
                        <div class="stat-value">{{ number_format($pendingReservationCount) }}</div>
                        <div class="stat-title">Reservasi menunggu review</div>
                        <div class="stat-foot">Menunggu keputusan admin sebelum dijadwalkan.</div>
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
                            <span class="stat-label">DISETUJUI</span>
                        </div>
                        <div class="stat-value">{{ number_format($approvedReservationCount) }}</div>
                        <div class="stat-title">Reservasi disetujui</div>
                        <div class="stat-foot">Sudah diproses dan siap dijadwalkan.</div>
                    </article>

                    <article class="stat-card stat-blue">
                        <div class="stat-card-top">
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="3.5" y="5" width="17" height="15.5" rx="2" stroke="currentColor"
                                        stroke-width="1.7" />
                                    <path d="M7.5 3v4M16.5 3v4M3.5 9.5h17" stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" />
                                </svg>
                            </div>
                            <span class="stat-label">HARI INI</span>
                        </div>
                        <div class="stat-value">{{ number_format($todayReservationCount) }}</div>
                        <div class="stat-title">Reservasi untuk hari ini</div>
                        <div class="stat-foot">Reservasi Pending atau Approved yang berlangsung hari ini.</div>
                    </article>

                    <article class="stat-card stat-purple">
                        <div class="stat-card-top">
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="5" y="4" width="14" height="17" rx="2.5" stroke="currentColor"
                                        stroke-width="1.7" />
                                    <path d="M9 3h6v3H9z" stroke="currentColor" stroke-width="1.7"
                                        stroke-linejoin="round" />
                                    <path d="M8.5 11h7M8.5 15h5" stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" />
                                </svg>
                            </div>
                            <span class="stat-label">TOTAL</span>
                        </div>
                        <div class="stat-value">{{ number_format($totalReservationCount) }}</div>
                        <div class="stat-title">Seluruh reservasi</div>
                        <div class="stat-foot">Termasuk riwayat reservasi yang pernah Anda buat.</div>
                    </article>
                </div>

                <section class="dashboard-panel">
                    <div class="panel-heading">
                        <div>
                            <div class="panel-title">Reservasi Layanan</div>
                            <div class="panel-subtitle">
                                Daftar reservasi layanan
                            </div>
                        </div>

                        <a class="panel-link" href="{{ route('reservations.index') }}">
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
                                    <th>NOMOR / ORDER</th>
                                    <th>TANGGAL LAYANAN</th>
                                    <th>PELANGGAN</th>
                                    <th>TIPE LAYANAN</th>
                                    <th>TAMU</th>
                                    <th>STATUS</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reservations as $reservation)
                                    <tr>
                                        <td>
                                            <button type="button" class="service-code"
                                                data-reservation-detail-open="reservationDetailModal{{ $reservation->id }}">
                                                {{ $reservation->reservation_number }}
                                            </button>
                                            <div class="service-spk">
                                                {{ $reservation->order_number ? 'Order: ' . $reservation->order_number : 'Tanpa nomor order' }}
                                            </div>
                                            <div class="service-spk">
                                                Reservasi: {{ $reservation->requester?->name ?? '—' }}
                                            </div>
                                        </td>
                                        <td class="table-date">
                                            {{ \App\Support\DateFormatter::format($reservation->service_date) }}
                                        </td>
                                        <td class="table-customer">{{ $reservation->customer_name }}</td>
                                        <td>{{ $reservation->serviceType?->name ?? '—' }}</td>
                                        <td>{{ number_format($reservation->guest_count) }} orang</td>
                                        <td>
                                            <span
                                                class="resv-status {{ $reservation->status }}">{{ $reservation->status }}</span>
                                        </td>
                                        <td>
                                            <button type="button" class="services-detail-btn"
                                                data-reservation-detail-open="reservationDetailModal{{ $reservation->id }}">
                                                Detail
                                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor"
                                                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
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
                                                        <path d="M7 9h10M7 13h6" stroke="currentColor"
                                                            stroke-width="1.6" stroke-linecap="round" />
                                                    </svg>
                                                </div>
                                                <strong>Belum ada reservasi layanan tersedia</strong>
                                                <span>Reservasi berstatus Pending dan Approved akan muncul di sini.</span>
                                                <a href="{{ route('reservations.create') }}">
                                                    Ajukan reservasi pertama
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="panel-footer">
                        Menampilkan {{ $reservations->count() }} reservasi layanan
                    </div>
                </section>
            </div>

            <aside class="tms-info-panel" aria-label="Informasi Ringkasan Reservasi">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Ringkasan Reservasi</h2>
                    <p>Status reservasi layanan yang Anda ajukan saat ini.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Menunggu persetujuan</span>
                        <strong class="tms-info-value">{{ number_format($pendingReservationCount) }}</strong>
                        <span class="tms-info-description">Reservasi yang masih berstatus Pending.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Disetujui</span>
                        <strong class="tms-info-value">{{ number_format($approvedReservationCount) }}</strong>
                        <span class="tms-info-description">Reservasi yang telah disetujui admin.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Ditolak</span>
                        <strong class="tms-info-value">{{ number_format($rejectedReservationCount) }}</strong>
                        <span class="tms-info-description">Reservasi yang ditolak admin.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Dibatalkan</span>
                        <strong class="tms-info-value">{{ number_format($cancelledReservationCount) }}</strong>
                        <span class="tms-info-description">Reservasi yang dibatalkan.</span>
                    </div>
                </div>
                <div class="tms-info-panel-footer">
                    <a href="{{ route('reservations.index') }}">Buka reservasi →</a>
                    <span aria-hidden="true"> · </span>
                    <a href="{{ route('reservations.create') }}">Ajukan reservasi →</a>
                </div>
            </aside>
        </div>
    </div>

    @foreach ($reservations as $reservation)
        @include('reservations._detail-modal', ['reservation' => $reservation])
    @endforeach

    <script>
        (() => {
            const modals = document.querySelectorAll('.reservation-detail-modal');
            const closeModal = (modal) => {
                modal.hidden = true;
                document.body.classList.remove('tms-modal-open');
            };

            document.querySelectorAll('[data-reservation-detail-open]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    const modal = document.getElementById(trigger.dataset.reservationDetailOpen);
                    if (!modal) return;

                    modal.hidden = false;
                    document.body.classList.add('tms-modal-open');
                    modal.querySelector('.tms-modal-close')?.focus();
                });
            });

            modals.forEach((modal) => {
                modal.querySelectorAll('[data-reservation-detail-close]').forEach((element) => {
                    element.addEventListener('click', () => closeModal(modal));
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') return;

                modals.forEach((modal) => {
                    if (!modal.hidden) closeModal(modal);
                });
            });
        })();
    </script>
@endsection
