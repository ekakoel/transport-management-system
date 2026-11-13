@php
    $guestCount = $reservation->guest_count;
    $itineraryStops = is_array($reservation->itinerary) ? array_values($reservation->itinerary) : [];
    $transportService = $reservation->transportService;
@endphp

<div class="tms-modal reservation-detail-modal" id="reservationDetailModal{{ $reservation->id }}" hidden>
    <div class="tms-modal-backdrop" data-reservation-detail-close></div>
    <div class="tms-modal-dialog reservation-detail-dialog" role="dialog" aria-modal="true"
        aria-labelledby="reservationDetailTitle{{ $reservation->id }}">
        <div class="tms-modal-header">
            <div class="tms-modal-title">
                <h2 id="reservationDetailTitle{{ $reservation->id }}">Detail Reservasi Layanan</h2>
                <p>{{ $reservation->reservation_number }} · {{ $reservation->requester?->name ?? '—' }}</p>
            </div>
            <button type="button" class="tms-modal-close" data-reservation-detail-close
                aria-label="Tutup">&times;</button>
        </div>

        <div class="tms-modal-body">
            <div class="reservation-detail-meta">
                <span class="resv-status {{ $reservation->status }}">{{ $reservation->status }}</span>
                <span class="resv-muted">
                    Diajukan {{ \App\Support\DateFormatter::format($reservation->created_at, null, 'd M Y H:i') }}
                </span>
            </div>

            <div class="service-show-details-grid reservation-detail-grid">
                <div class="service-show-detail-item">
                    <div>
                        <span class="service-show-detail-label">Reservasi</span>
                        <strong>{{ $reservation->requester?->name ?? '—' }}</strong>
                        <small>{{ $reservation->requester?->phone ?: 'Nomor telepon tidak tersedia' }}</small>
                    </div>
                </div>
                <div class="service-show-detail-item">
                    <div>
                        <span class="service-show-detail-label">Nomor Order</span>
                        <strong>{{ $reservation->order_number ?: '—' }}</strong>
                    </div>
                </div>
                <div class="service-show-detail-item">
                    <div>
                        <span class="service-show-detail-label">Tipe Layanan</span>
                        <strong>{{ $reservation->serviceType?->name ?? '—' }}</strong>
                    </div>
                </div>
                <div class="service-show-detail-item">
                    <div>
                        <span class="service-show-detail-label">Tanggal Layanan</span>
                        <strong>
                            {{ \App\Support\DateFormatter::format($reservation->service_date) }}
                            <span class="service-show-time-separator">&#8594;</span>
                            {{ \App\Support\DateFormatter::format($reservation->service_end_date) }}
                        </strong>
                    </div>
                </div>
                <div class="service-show-detail-item">
                    <div>
                        <span class="service-show-detail-label">Jumlah Tamu</span>
                        <strong>{{ number_format($guestCount) }} orang</strong>
                    </div>
                </div>
                <div class="service-show-detail-item">
                    <div>
                        <span class="service-show-detail-label">Catatan Reservasi</span>
                        <strong class="service-show-notes">{{ $reservation->notes ?: '—' }}</strong>
                    </div>
                </div>
            </div>

            <section class="reservation-detail-section">
                <div class="reservation-detail-section-header">
                    <h3>Daftar Tamu</h3>
                    <span>Jumlah Tamu: {{ number_format($guestCount) }}</span>
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
                            @forelse ($reservation->guests as $guest)
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
                                        <div class="services-primary-text">{{ $guest->phone ?: '-' }}</div>
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

            <section class="reservation-detail-section">
                <div class="reservation-detail-section-header">
                    <h3>Itinerary</h3>
                    <span>{{ count($itineraryStops) }} destinasi</span>
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
                            @forelse ($itineraryStops as $stop)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="services-primary-text">{{ $stop['visit_time'] ?? '—' }}</div>
                                    </td>
                                    <td>
                                        <div class="services-primary-text">{{ $stop['name'] ?? 'Destinasi' }}</div>
                                    </td>
                                    <td>
                                        @if (!empty($stop['google_maps_url']))
                                            <div class="services-primary-text">
                                                <a href="{{ $stop['google_maps_url'] }}" target="_blank"
                                                    rel="noopener noreferrer">Buka Google Maps</a>
                                            </div>
                                        @else
                                            <div class="services-primary-text">—</div>
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
                        </tbody>
                    </table>
                </div>
            </section>

            @if ($transportService)
                <section class="reservation-detail-section">
                    <div class="reservation-detail-section-header">
                        <h3>Penugasan Layanan</h3>
                        <span>{{ $transportService->service_code }}</span>
                    </div>
                    <div class="service-show-details-grid reservation-detail-grid">
                        <div class="service-show-detail-item">
                            <div>
                                <span class="service-show-detail-label">Driver</span>
                                <strong>{{ $transportService->driver?->name ?? 'Belum ditugaskan' }}</strong>
                                <small>{{ $transportService->driver?->phone ?? 'Nomor telepon tidak tersedia' }}</small>
                            </div>
                        </div>
                        <div class="service-show-detail-item">
                            <div>
                                <span class="service-show-detail-label">Kendaraan</span>
                                <strong>{{ $transportService->vehicle?->name ?? 'Belum ditentukan' }}</strong>
                                <small>{{ $transportService->plate_number ?: 'Nomor polisi tidak tersedia' }}</small>
                            </div>
                        </div>
                        <div class="service-show-detail-item">
                            <div>
                                <span class="service-show-detail-label">Waktu Layanan</span>
                                <strong>
                                    {{ \App\Support\DateFormatter::format($transportService->service_date, $transportService->start_time) }}
                                    <span class="service-show-time-separator">&#8594;</span>
                                    {{ \App\Support\DateFormatter::format($transportService->service_end_date, $transportService->end_time) }}
                                </strong>
                            </div>
                        </div>
                        <div class="service-show-detail-item">
                            <div>
                                <span class="service-show-detail-label">Layanan / SPK</span>
                                <strong>{{ $transportService->service_code }}</strong>
                                @if ($transportService->spk)
                                    <small>{{ $transportService->spk->spk_number }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            @if ($reservation->admin_notes)
                <section class="reservation-detail-section">
                    <div class="reservation-detail-section-header">
                        <h3>Catatan Admin</h3>
                    </div>
                    <p class="reservation-detail-notes">{{ $reservation->admin_notes }}</p>
                </section>
            @endif
        </div>

        <div class="tms-modal-footer">
            <button type="button" class="tms-modal-btn tms-modal-btn-secondary"
                data-reservation-detail-close>Tutup</button>
        </div>
    </div>
</div>
