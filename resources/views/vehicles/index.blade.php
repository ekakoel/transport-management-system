@extends('layouts.app')

@section('title', 'Kendaraan')

@section('content')
    <header>
        {{-- Header --}}
        <div class="main-header">
            <div>
                <div class="service-create-eyebrow">
                    <span></span>
                    TRANSPORT OPERATIONS
                </div>
                <h1>Manajemen Kendaraan</h1>
                <p>Kelola armada, nomor polisi, kapasitas penumpang, dan status operasional kendaraan.</p>
            </div>

            <button type="button" class="services-create-btn" data-vehicle-create-open
                aria-haspopup="dialog" aria-controls="vehicleCreateModal">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                <span>Tambah Kendaraan</span>
            </button>
        </div>
        {{-- Information Banner --}}
        <div class="main-notice mb-3">
            <span class="main-notice-icon">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7" />
                    <path d="M12 11v5m0-8h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </span>
            <div>
                <strong>Pastikan data kendaraan selalu akurat</strong>
                <p>Perbarui informasi kendaraan dan status operasionalnya agar proses penugasan transportasi
                    berjalan dengan baik.</p>
            </div>
        </div>
    </header>

    @php
        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        $calendarLabel = $monthNames[$calendarMonth->month] . ' ' . $calendarMonth->year;
        $leadingDays = $calendarMonth->copy()->startOfMonth()->dayOfWeekIso - 1;
        $daysInMonth = $calendarMonth->daysInMonth;
        $trailingDays = (7 - (($leadingDays + $daysInMonth) % 7)) % 7;
        $prevMonth = $calendarMonth->copy()->subMonth();
        $nextMonth = $calendarMonth->copy()->addMonth();
        $todayString = now('Asia/Makassar')->toDateString();
    @endphp

    <section class="vehicle-calendar-panel">
        {{-- <div class="vehicle-calendar-toolbar">
            <div>
                <span class="vehicle-calendar-kicker">KETERSEDIAAN ARMADA</span>
                <h2>Kalender Ketersediaan Kendaraan</h2>
                <p>Jadwal layanan transportasi per bulan untuk melihat kendaraan yang sudah terpakai.</p>
            </div>

            <div class="vehicle-calendar-toolbar-actions">
                <a href="{{ route('vehicles.index', ['month' => $prevMonth->format('Y-m')]) }}"
                    class="vehicle-calendar-nav-btn" aria-label="Bulan sebelumnya">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m15 6-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </a>
                <span class="vehicle-calendar-month">{{ $calendarLabel }}</span>
                <a href="{{ route('vehicles.index', ['month' => $nextMonth->format('Y-m')]) }}"
                    class="vehicle-calendar-nav-btn" aria-label="Bulan berikutnya">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m9 6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </a>
                @if (! $calendarMonth->isSameMonth(now('Asia/Makassar')))
                    <a href="{{ route('vehicles.index') }}" class="vehicle-calendar-today-btn">Bulan ini</a>
                @endif
            </div>
        </div> --}}

        
    </section>

    <div class="tms-modal" id="calendarEventModal" hidden>
        <div class="tms-modal-backdrop" data-calendar-modal-close></div>
        <div class="tms-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="calendarEventTitle">
            <div class="tms-modal-header">
                <h2 id="calendarEventTitle">Detail Layanan</h2>
                <button type="button" class="tms-modal-close" data-calendar-modal-close
                    aria-label="Tutup">&times;</button>
            </div>

            <div class="tms-modal-body">
                <div class="vehicle-calendar-detail-list">
                    <div class="vehicle-calendar-detail-item">
                        <span class="vehicle-calendar-detail-label">Kendaraan</span>
                        <strong id="calendarEventVehicle">—</strong>
                    </div>

                    <div class="vehicle-calendar-detail-item">
                        <span class="vehicle-calendar-detail-label">Tipe Layanan</span>
                        <strong id="calendarEventType">—</strong>
                    </div>

                    <div class="vehicle-calendar-detail-item">
                        <span class="vehicle-calendar-detail-label">Driver</span>
                        <strong id="calendarEventDriver">—</strong>
                    </div>

                    <div class="vehicle-calendar-detail-item">
                        <span class="vehicle-calendar-detail-label">Waktu Layanan</span>
                        <strong id="calendarEventTime">—</strong>
                        <small>Waktu setempat Bali (WITA)</small>
                    </div>
                </div>
            </div>

            <div class="tms-modal-footer">
                <button type="button" class="tms-modal-btn tms-modal-btn-secondary"
                    data-calendar-modal-close>Tutup</button>
                <a href="#" id="calendarEventLink" class="tms-modal-btn tms-modal-btn-primary">
                    Buka Detail Layanan
                </a>
            </div>
        </div>
    </div>

    <div class="tms-modal" id="vehicleCreateModal" data-open="{{ $errors->any() ? '1' : '0' }}" hidden>
        <div class="tms-modal-backdrop" data-vehicle-create-close></div>
        <div class="tms-modal-dialog vehicle-create-dialog" role="dialog" aria-modal="true"
            aria-labelledby="vehicleCreateTitle">
            <div class="tms-modal-header">
                <div>
                    <h2 id="vehicleCreateTitle">Tambah Kendaraan</h2>
                    <p class="vehicle-create-subtitle">Masukkan informasi kendaraan baru ke dalam sistem.</p>
                </div>
                <button type="button" class="tms-modal-close" data-vehicle-create-close
                    aria-label="Tutup">&times;</button>
            </div>

            <form id="vehicle-form" method="POST" action="{{ route('vehicles.store') }}">
                @csrf

                <div class="tms-modal-body">
                    <div class="service-create-fields">
                        <div class="service-create-field">
                            <label for="name">Nama / Model Kendaraan <span>*</span></label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}"
                                placeholder="Contoh: Toyota Innova Zenix" required>
                            @error('name')
                                <small class="service-create-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="service-create-field">
                            <label for="plate_number">Nomor Polisi <span>*</span></label>
                            <input id="plate_number" name="plate_number" type="text"
                                value="{{ old('plate_number') }}" placeholder="Contoh: DK 1234 ABC" required>
                            @error('plate_number')
                                <small class="service-create-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="service-create-field">
                            <label for="capacity">Kapasitas Penumpang <span>*</span></label>
                            <input id="capacity" name="capacity" type="number" min="1" max="100"
                                value="{{ old('capacity', 6) }}" placeholder="Jumlah penumpang" required>
                            @error('capacity')
                                <small class="service-create-error">{{ $message }}</small>
                            @enderror
                            <small class="service-create-help">
                                Masukkan kapasitas penumpang, tidak termasuk pengemudi.
                            </small>
                        </div>

                        <div class="service-create-field">
                            <label for="status">Status Operasional <span>*</span></label>
                            <select id="status" name="status" required>
                                <option value="Available" @selected(old('status', 'Available') === 'Available')>
                                    Available
                                </option>
                                <option value="Assigned" @selected(old('status') === 'Assigned')>
                                    Assigned
                                </option>
                                <option value="Maintenance" @selected(old('status') === 'Maintenance')>
                                    Maintenance
                                </option>
                                <option value="Inactive" @selected(old('status') === 'Inactive')>
                                    Inactive
                                </option>
                            </select>
                            @error('status')
                                <small class="service-create-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="service-create-field service-field-full">
                            <label for="notes">Catatan Kendaraan</label>
                            <textarea id="notes" name="notes" rows="3"
                                placeholder="Informasi tambahan, kondisi kendaraan, atau catatan operasional...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <small class="service-create-error">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="tms-modal-footer">
                    <button type="button" class="tms-modal-btn tms-modal-btn-secondary"
                        data-vehicle-create-close>Batal</button>
                    <button type="submit" class="tms-modal-btn tms-modal-btn-primary vehicle-create-submit">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" />
                        </svg>
                        Simpan Kendaraan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="service-create-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
                <section class="service-create-card mb-3">
                    <div class="service-create-section-header">
                        <div class="service-create-section-icon service-icon-green">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="m5 11 2-5h10l2 5M4 11h16v7H4z" stroke="currentColor" stroke-width="1.7"
                                    stroke-linejoin="round" />
                                <path d="M7 18v2m10-2v2" stroke="currentColor" stroke-width="1.7"
                                    stroke-linecap="round" />
                                <circle cx="7.5" cy="14.5" r="1.3" stroke="currentColor"
                                    stroke-width="1.5" />
                                <circle cx="16.5" cy="14.5" r="1.3" stroke="currentColor"
                                    stroke-width="1.5" />
                            </svg>
                        </div>
                        <div>
                            <span class="vehicle-calendar-kicker">KETERSEDIAAN ARMADA</span>
                            <h2>Kalender Ketersediaan Kendaraan</h2>
                            <p>Jadwal layanan transportasi per bulan untuk melihat kendaraan yang sudah terpakai.</p>
                        </div>

                        <div class="vehicle-calendar-toolbar-actions">
                            <a href="{{ route('vehicles.index', ['month' => $prevMonth->format('Y-m')]) }}"
                                class="vehicle-calendar-nav-btn" aria-label="Bulan sebelumnya">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m15 6-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                            <span class="vehicle-calendar-month">{{ $calendarLabel }}</span>
                            <a href="{{ route('vehicles.index', ['month' => $nextMonth->format('Y-m')]) }}"
                                class="vehicle-calendar-nav-btn" aria-label="Bulan berikutnya">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m9 6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                            @if (! $calendarMonth->isSameMonth(now('Asia/Makassar')))
                                <a href="{{ route('vehicles.index') }}" class="vehicle-calendar-today-btn">Bulan ini</a>
                            @endif
                        </div>
                    </div>
                    
                    
                        <div class="vehicle-calendar-weekdays">
                            @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $weekday)
                                <span>{{ $weekday }}</span>
                            @endforeach
                        </div>

                        <div class="vehicle-calendar-grid">
                            @for ($i = 0; $i < $leadingDays; $i++)
                                <div class="vehicle-calendar-day is-empty"></div>
                            @endfor

                            @for ($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $calendarDate = $calendarMonth->copy()->day($day);
                                    $dayEvents = $calendarEvents[$calendarDate->format('Y-m-d')] ?? [];
                                    $isToday = $calendarDate->isToday();
                                    $isPastDay = ! $isToday && $calendarDate->toDateString() < $todayString;
                                @endphp
                                <div
                                    class="vehicle-calendar-day {{ $isPastDay ? 'is-past' : '' }} {{ $isToday ? 'is-today' : '' }} {{ !empty($dayEvents) ? 'has-events' : '' }}"
                                    @if ($isToday) aria-current="date" @endif>
                                    <div class="vehicle-calendar-day-head">
                                        <span class="vehicle-calendar-date">{{ $day }}</span>
                                        @if ($isToday)
                                            <span class="vehicle-calendar-today-label">Hari ini</span>
                                        @endif
                                        @if (!empty($dayEvents))
                                            <span class="vehicle-calendar-badge">{{ count($dayEvents) }}</span>
                                        @endif
                                    </div>

                                    @if (!empty($dayEvents))
                                        <div class="vehicle-calendar-events">
                                            @foreach (array_slice($dayEvents, 0, 3) as $event)
                                                <a href="{{ $event['url'] }}" class="vehicle-calendar-event"
                                                    data-calendar-event
                                                    data-vehicle="{{ $event['vehicle'] }}"
                                                    data-plate="{{ $event['plate'] }}"
                                                    data-type="{{ $event['type'] }}"
                                                    data-driver="{{ $event['driver'] }}"
                                                    data-start="{{ $event['start'] }}"
                                                    data-end="{{ $event['end'] }}"
                                                    data-code="{{ $event['code'] }}"
                                                    data-url="{{ $event['url'] }}"
                                                    title="{{ $event['vehicle'] }} ({{ $event['plate'] }}) : {{ $event['type'] }} ({{ $event['code'] }})">
                                                    <span class="vehicle-calendar-event-dot {{ $event['status'] }}"
                                                        style="background: {{ $vehicleColors[$event['vehicle_id']] ?? '#94a3b8' }}"></span>
                                                    <span class="vehicle-calendar-event-text">
                                                        {{ $event['vehicle'] }} ({{ $event['plate'] }}) : {{ $event['type'] }}
                                                    </span>
                                                </a>
                                            @endforeach

                                            @if (count($dayEvents) > 3)
                                                <div class="vehicle-calendar-more">+{{ count($dayEvents) - 3 }} jadwal lainnya
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endfor

                            @for ($i = 0; $i < $trailingDays; $i++)
                                <div class="vehicle-calendar-day is-empty"></div>
                            @endfor
                        </div>
                    

                        
                </section>
                {{-- Daftar Kendaraan --}}
                <section class="service-create-card">
                    <div class="service-create-section-header">
                        <div class="service-create-section-icon service-icon-green">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="m5 11 2-5h10l2 5M4 11h16v7H4z" stroke="currentColor" stroke-width="1.7"
                                    stroke-linejoin="round" />
                                <path d="M7 18v2m10-2v2" stroke="currentColor" stroke-width="1.7"
                                    stroke-linecap="round" />
                                <circle cx="7.5" cy="14.5" r="1.3" stroke="currentColor"
                                    stroke-width="1.5" />
                                <circle cx="16.5" cy="14.5" r="1.3" stroke="currentColor"
                                    stroke-width="1.5" />
                            </svg>
                        </div>
                        <div>
                            <h2>Daftar Kendaraan</h2>
                            <p>Informasi armada yang terdaftar dalam sistem.</p>
                        </div>
                        <span class="service-create-step">{{ $vehicles->total() }}</span>
                    </div>
                    <div class="services-table-scroll">
                        <table class="services-table">
                            <thead>
                                <tr>
                                    <th>Kendaraan</th>
                                    <th>Nomor Polisi</th>
                                    <th>Kapasitas</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($vehicles as $v)
                                    <tr>
                                        <td><strong>{{ $v->name }}</strong></td>
                                        <td>{{ $v->plate_number }}</td>
                                        <td>{{ $v->capacity }} pax</td>
                                        <td>
                                            <span class="badge">{{ $v->status }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('vehicles.edit', $v) }}" class="services-detail-btn">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 28px;">
                                            Belum ada kendaraan terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($vehicles->hasPages())
                        <div class="tms-pagination-shell">
                            {{ $vehicles->links() }}
                        </div>
                    @endif
                </section>
            </div>

            
            
            <aside class="tms-info-panel" aria-label="Informasi Kendaraan">
                <div class="vehicle-calendar-legend-heading">
                    <h2>Informasi Kendaraan</h2>
                    <p>Warna berikut digunakan pada kalender untuk menandai kendaraan yang memiliki jadwal.</p>
                </div>
    
                <div class="vehicle-calendar-legend-summary">
                    {{ number_format(count($vehicleCalendarCounts)) }} kendaraan memiliki jadwal pada bulan ini.
                </div>
    
                <div class="vehicle-calendar-legend-list mb-4">
                    @forelse ($calendarVehicles as $calendarVehicle)
                        <div class="vehicle-calendar-legend-item">
                            <span class="vehicle-calendar-swatch"
                                style="background: {{ $vehicleColors[$calendarVehicle->id] }}"></span>
                            <div class="vehicle-calendar-legend-text">
                                <strong>{{ $calendarVehicle->name }}</strong>
                                <span>{{ $calendarVehicle->plate_number }} &middot; {{ $calendarVehicle->status }}</span>
                            </div>
                            <span class="vehicle-calendar-legend-count">
                                {{ $vehicleCalendarCounts[$calendarVehicle->id] ?? 0 }} hari
                            </span>
                        </div>
                    @empty
                        <p class="vehicle-calendar-legend-empty">Belum ada kendaraan terdaftar.</p>
                    @endforelse
                </div>

                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Kendaraan</h2>
                    <p>Ringkasan armada berdasarkan status saat ini.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Total kendaraan</span>
                        <strong class="tms-info-value">{{ number_format($vehicleStats['total']) }}</strong>
                        <span class="tms-info-description">Seluruh kendaraan terdaftar.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Available</span>
                        <strong class="tms-info-value">{{ number_format($vehicleStats['Available']) }}</strong>
                        <span class="tms-info-description">Kendaraan tersedia.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Assigned</span>
                        <strong class="tms-info-value">{{ number_format($vehicleStats['Assigned']) }}</strong>
                        <span class="tms-info-description">Kendaraan ditugaskan.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Maintenance</span>
                        <strong class="tms-info-value">{{ number_format($vehicleStats['Maintenance']) }}</strong>
                        <span class="tms-info-description">Kendaraan dalam perawatan.</span>
                    </div>
                </div>
                <div class="tms-info-panel-footer">
                    <button type="button" class="tms-info-add-btn" data-vehicle-create-open
                        aria-haspopup="dialog" aria-controls="vehicleCreateModal">
                        Tambah kendaraan baru
                    </button>
                </div>
            </aside>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.getElementById('vehicleCreateModal');
            if (!modal) return;

            const openers = document.querySelectorAll('[data-vehicle-create-open]');
            const focusField = modal.querySelector('#name');

            const openModal = () => {
                modal.hidden = false;
                document.body.classList.add('tms-modal-open');
                window.setTimeout(() => focusField?.focus(), 30);
            };

            const closeModal = () => {
                modal.hidden = true;
                document.body.classList.remove('tms-modal-open');
            };

            openers.forEach((opener) => opener.addEventListener('click', openModal));

            modal.querySelectorAll('[data-vehicle-create-close]').forEach((element) => {
                element.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.hidden) {
                    closeModal();
                }
            });

            if (modal.dataset.open === '1') {
                openModal();
            }
        })();
    </script>

    <script>
        (() => {
            const modal = document.getElementById('calendarEventModal');
            if (!modal) return;

            const vehicleField = document.getElementById('calendarEventVehicle');
            const typeField = document.getElementById('calendarEventType');
            const driverField = document.getElementById('calendarEventDriver');
            const timeField = document.getElementById('calendarEventTime');
            const detailLink = document.getElementById('calendarEventLink');
            const closeButton = modal.querySelector('.tms-modal-close');
            const triggers = document.querySelectorAll('[data-calendar-event]');

            const openModal = (trigger) => {
                const plate = trigger.dataset.plate;
                vehicleField.textContent = (trigger.dataset.vehicle || 'Kendaraan') + (plate && plate !== '-' ? ' (' + plate + ')' : '');
                typeField.textContent = trigger.dataset.type || '—';
                driverField.textContent = trigger.dataset.driver || 'Belum ditugaskan';
                timeField.textContent = (trigger.dataset.start || '—') + ' → ' + (trigger.dataset.end || '—');
                detailLink.href = trigger.dataset.url || '#';

                modal.hidden = false;
                document.body.classList.add('tms-modal-open');
                window.setTimeout(() => closeButton?.focus(), 30);
            };

            const closeModal = () => {
                modal.hidden = true;
                document.body.classList.remove('tms-modal-open');
            };

            triggers.forEach((trigger) => {
                trigger.addEventListener('click', (event) => {
                    event.preventDefault();
                    openModal(trigger);
                });
            });

            modal.querySelectorAll('[data-calendar-modal-close]').forEach((element) => {
                element.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.hidden) {
                    closeModal();
                }
            });
        })();
    </script>
@endsection
