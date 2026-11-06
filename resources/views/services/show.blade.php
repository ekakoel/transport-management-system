@extends('layouts.app')

@section('title', 'Detail SPK')

@section('content')
    @php
        $statusClass = match ($service->status) {
            'Draft' => 'draft',
            'Published' => 'published',
            'In Progress' => 'progress',
            'Completed' => 'completed',
            'Cancelled' => 'cancelled',
            'Expired' => 'expired',
            default => 'default',
        };
        $isAdmin = strtolower((string) auth()->user()->role) === 'admin';
        $isLocked = $service->isLocked();
        $canEditGuests = $isAdmin && ! $isLocked;
        $canEditItinerary = $isAdmin && ! $isLocked;
    @endphp
    <header>
        <div class="main-header">
            <div class="service-heading">
                <div class="service-show-eyebrow">
                    <span></span>
                    TRANSPORT OPERATIONS / DETAIL SPK
                </div>

                <h1>{{ $service->service_code }}</h1>

                <p>
                    Nomor SPK:
                    <strong>{{ $service->spk?->spk_number ?: 'Belum diterbitkan' }}</strong>
                    <span class="service-show-separator">&#10132;</span>
                    Detail layanan dan laporan driver
                </p>
            </div>

            <a href="{{ route('services.index') }}" class="service-show-back">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M19 12H5m7 7-7-7 7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                Semua Layanan
            </a>
        </div>
    </header>
    <div class="service-show-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
                {{-- Public SPK Link --}}
                @if ($isAdmin && $newUrl && !request()->routeIs('services.spk.preview'))
                    <section class="service-show-public-link">
                        <div class="service-show-public-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M10 13a5 5 0 0 0 7.1 0l3-3A5 5 0 0 0 13 2.9l-1.7 1.7" stroke="currentColor"
                                    stroke-width="1.8" stroke-linecap="round" />
                                <path d="M14 11a5 5 0 0 0-7.1 0l-3 3A5 5 0 0 0 11 21.1l1.7-1.7" stroke="currentColor"
                                    stroke-width="1.8" stroke-linecap="round" />
                            </svg>
                        </div>

                        <div class="service-show-public-content">
                            <div class="service-title">
                                <h2>Tautan Publik SPK</h2>
                                <p>
                                    @if ($isLocked)
                                        Layanan sudah berlalu. Tautan publik SPK hanya dapat dibuka.
                                    @else
                                        {{ $isAdmin ? 'Kirim SPK kepada driver dan detail pesanan ke Reservasi.' : 'Buka dokumen SPK untuk melihat detail layanan.' }}
                                    @endif
                                </p>
                            </div>

                            <div class="service-show-url-row">
                                <a href="{{ $isAdmin ? $newUrl : route('services.spk.preview', $service) }}" target="_blank" rel="noopener noreferrer" id="publicSpkUrl">
                                    <button class="service-show-copy-btn">
                                        {!! $isAdmin ? 'Lihat<br>SPK' : 'Lihat<br>SPK' !!}
                                    </button>
                                </a>

                                @if($isAdmin && ! $isLocked)
                                <form method="post" action="{{ route('services.spk.send', $service) }}"
                                    target="_blank" id="sendSpkForm">
                                    @csrf
                                    <button type="submit" class="service-show-copy-btn" id="sendSpkButton"
                                        @disabled(!$service->driver?->phone)>
                                        <span>{!! $spkSentCount > 0 ? 'Kirim SPK<br>Driver' : 'Kirim SPK<br>Driver' !!}</span>
                                    </button>
                                </form>

                                <form method="post" action="{{ route('services.info.send', $service) }}"
                                    target="_blank" id="sendInfoForm">
                                    @csrf
                                    <button type="submit" class="service-show-copy-btn" id="sendInfoButton"
                                        @disabled(!$service->reservation || !($service->reservation->requester?->phone ?: $service->reservation->customer_phone ?: $service->customer_phone))>
                                        <span>{!! $infoSentCount > 0 ? 'Kirim Ulang Info<br>Reservasi' : 'Kirim Info<br>Reservasi' !!}</span>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </section>
                @endif

                {{-- Summary Cards --}}
                <div class="service-show-summary">

                    <section class="service-show-card">
                        <div class="service-show-card-top">
                            <div class="service-show-card-icon service-show-blue">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="3.5" y="5" width="17" height="15.5" rx="2" stroke="currentColor"
                                        stroke-width="1.7" />
                                    <path d="M7.5 3v4M16.5 3v4M3.5 9.5h17" stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" />
                                </svg>
                            </div>
                            <span class="service-show-status service-show-status-{{ $statusClass }}">
                                <span></span>
                                {{ $service->status }}
                            </span>
                        </div>

                        <div class="service-show-card-label">Reservasi:</div>
                        <div class="service-show-status-row">
                            <div class="service-show-summary-value">
                                {{ $service->reservation?->requester?->name ?? 'Belum ditetapkan' }}
                            </div>
                            @if($service->reservation?->requester?->phone)
                                <div class="service-show-summary-sub">
                                    {{ $service->reservation->requester->phone }}
                                </div>
                            @endif
                        </div>

                        <div class="service-show-card-divider"></div>

                        <div class="service-show-detail-label">Agent / Pelanggan:</div>
                        <div class="service-show-summary-value">
                            {{ $service->customer_name }}
                        </div>
                        <div class="service-show-summary-sub">
                            {{ $service->customer_phone ?: '-' }}
                        </div>
                        
                        <div class="service-show-card-divider"></div>

                        <div class="service-show-detail-label">Tanggal Layanan</div>
                        <div class="service-show-summary-value">
                            {{ \App\Support\DateFormatter::format($service->service_date) }}
                        </div>
                        <div class="service-show-summary-sub">
                            {{ $service->serviceType?->name ?? '-' }}
                        </div>
                    </section>

                    <section class="service-show-card">
                        <div class="service-show-card-top">
                            <div class="service-show-card-icon service-show-green">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="7" r="3.5"
                                        stroke="currentColor" stroke-width="1.7" />
                                    <path d="M4.5 21v-2a7.5 7.5 0 0 1 15 0v2"
                                        stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" />
                                    <path d="M8 14.5h8"
                                        stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" />
                                </svg>
                            </div>
                            <span class="service-show-mini-label">DRIVER ASSIGNMENT</span>
                        </div>

                        <div class="service-show-card-label">Driver Bertugas</div>
                        <div class="service-show-summary-value">
                            {{ $service->driver?->name ?? 'Belum ditugaskan' }}
                        </div>
                        <div class="service-show-summary-sub">
                            {{ $service->driver?->phone ?? 'Nomor telepon tidak tersedia' }}
                        </div>

                        <div class="service-show-card-divider"></div>

                        <div class="service-show-detail-label">Kendaraan</div>
                        <div class="service-show-summary-value">
                            {{ $service->vehicle?->name ?? 'Belum ditentukan' }}
                        </div>
                        <div class="service-show-summary-sub">
                            {{ $service->vehicle?->plate_number ?? 'Nomor polisi tidak tersedia' }}
                        </div>
                    </section>
                </div>

                {{-- Trip Details --}}
                <section class="service-show-panel">
                    <div class="service-show-panel-header">
                        <div class="service-show-panel-heading">
                            <div class="service-show-panel-icon service-show-blue">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 5h16M4 12h16M4 19h10" stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" />
                                    <circle cx="18" cy="18" r="3" stroke="currentColor"
                                        stroke-width="1.7" />
                                </svg>
                            </div>
                            <div>
                                <h2>Detail Perjalanan</h2>
                                <p>Informasi pelanggan, penjemputan, dan instruksi layanan.</p>
                            </div>
                        </div>
                    </div>

                    <div class="service-show-details-grid">
                        



                        <div class="service-show-detail-item">
                            <span class="service-show-detail-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor"
                                        stroke-width="1.7" />
                                    <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" />
                                </svg>
                            </span>
                            <div>
                                <span class="service-show-detail-label">Waktu Layanan</span>
                                <strong>
                                    {{ \App\Support\DateFormatter::format($service->service_date, $service->start_time) }}
                                    <span class="service-show-time-separator">&#8594;</span>
                                    {{ \App\Support\DateFormatter::format($service->service_end_date, $service->end_time) }}
                                </strong>
                                <small>Waktu setempat Bali (WITA)</small>
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
                                    {!! $service->notes ?: '-' !!}
                                </strong>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="service-show-panel">
                    <div class="service-show-panel-header">
                        <div class="service-show-panel-heading">
                            <div class="service-show-panel-icon service-show-orange">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="10" cy="7" r="4" stroke="currentColor" stroke-width="1.7" />
                                    <path d="M3 21v-1.5A4.5 4.5 0 0 1 7.5 15h5A4.5 4.5 0 0 1 17 19.5V21"
                                        stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                    <path d="M16 4a4 4 0 0 1 0 7.5m2 3a4.5 4.5 0 0 1 2 3.5V21" stroke="currentColor"
                                        stroke-width="1.7" stroke-linecap="round" />
                                </svg>
                            </div>
                            <div>
                                <h2>Daftar Tamu</h2>
                                <p>Pemilik kasus internal dan wisatawan yang menggunakan layanan.</p>
                            </div>
                        </div>
    
                        <div class="service-show-panel-actions">
                            <span class="service-show-count">
                                {{ $service->guests->count() }} guests
                            </span>
                            @if ($canEditGuests)
                                <button type="button" class="service-show-guest-add" data-guest-add>
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8"
                                            stroke-linecap="round" />
                                    </svg>
                                    Tambah Tamu
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="service-show-table-scroll">
                        <table class="service-show-activity-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Usia</th>
                                    <th>Telephone</th>
                                    @if ($canEditGuests)
                                        <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($service->guests as $guest)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $guest->name }}</td>
                                        <td>{{ $guest->gender }}</td>
                                        <td>{{ $guest->age_category }}</td>
                                        <td>{{ $guest->phone ?: '-' }}</td>
                                        @if ($canEditGuests)
                                            <td>
                                                <div class="service-show-guest-actions">
                                                    <button type="button" class="service-guest-btn service-guest-btn-edit"
                                                        data-guest-edit
                                                        data-id="{{ $guest->id }}"
                                                        data-action="{{ route('services.guests.update', [$service, $guest]) }}"
                                                        data-name="{{ $guest->name }}"
                                                        data-gender="{{ $guest->gender }}"
                                                        data-age-category="{{ $guest->age_category }}"
                                                        data-phone="{{ $guest->phone }}">
                                                        Edit
                                                    </button>
                                                    <button type="button" class="service-guest-btn service-guest-btn-delete"
                                                        data-guest-delete
                                                        data-name="{{ $guest->name }}"
                                                        data-action="{{ route('services.guests.destroy', [$service, $guest]) }}">
                                                        Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $canEditGuests ? 6 : 5 }}">
                                            <div class="empty-value">Data tamu tidak tersedia.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($isAdmin && ! $canEditGuests)
                        <p class="service-guest-locked">
                            Layanan yang sudah berlalu atau selesai tidak dapat diubah lagi, termasuk daftar tamu.
                        </p>
                    @endif
                    </section>

                    @if ($canEditGuests)
                        <div class="tms-modal" id="guestModal" hidden
                            data-open="{{ old('guest_context') ? '1' : '0' }}"
                            data-context="{{ old('guest_context', 'create') }}"
                            data-guest-id="{{ old('guest_id') }}"
                            data-edit-action="{{ old('guest_context') === 'edit' && old('guest_id') ? route('services.guests.update', [$service, old('guest_id')]) : '' }}">
                            <div class="tms-modal-backdrop" data-guest-modal-close></div>
                            <div class="tms-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="guestModalTitle">
                                <div class="tms-modal-header">
                                    <h2 id="guestModalTitle">Tambah Tamu</h2>
                                    <button type="button" class="tms-modal-close" data-guest-modal-close
                                        aria-label="Tutup">&times;</button>
                                </div>
                                <form id="guestForm" method="POST" action="{{ route('services.guests.store', $service) }}">
                                    @csrf
                                    <input type="hidden" name="_method" id="guestFormMethod" value="POST">
                                    <input type="hidden" name="guest_context" id="guestContext" value="create">
                                    <input type="hidden" name="guest_id" id="guestId" value="">
                                    <div class="tms-modal-body">
                                        <div class="service-create-field">
                                            <label for="guestName">Nama tamu <span>*</span></label>
                                            <input id="guestName" name="name" type="text" maxlength="150"
                                                value="{{ old('name') }}" required>
                                        </div>
                                        <div class="service-create-field">
                                            <label for="guestGender">Jenis kelamin <span>*</span></label>
                                            <select id="guestGender" name="gender" required>
                                                <option value="Male" @selected(old('gender') === 'Male')>Laki-laki</option>
                                                <option value="Female" @selected(old('gender') === 'Female')>Perempuan</option>
                                            </select>
                                        </div>
                                        <div class="service-create-field">
                                            <label for="guestAgeCategory">Kategori usia <span>*</span></label>
                                            <select id="guestAgeCategory" name="age_category" required>
                                                <option value="Adult" @selected(old('age_category') === 'Adult')>Dewasa</option>
                                                <option value="Children" @selected(old('age_category') === 'Children')>Anak-anak</option>
                                            </select>
                                        </div>
                                        <div class="service-create-field">
                                            <label for="guestPhone">Nomor telepon</label>
                                            <input id="guestPhone" name="phone" type="text" maxlength="40"
                                                value="{{ old('phone') }}">
                                        </div>
                                    </div>
                                    <div class="tms-modal-footer">
                                        <button type="button" class="tms-modal-btn tms-modal-btn-secondary"
                                            data-guest-modal-close>Batal</button>
                                        <button type="submit" class="tms-modal-btn tms-modal-btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="tms-modal" id="guestDeleteModal" hidden>
                            <div class="tms-modal-backdrop" data-guest-delete-close></div>
                            <div class="tms-modal-dialog tms-modal-dialog-sm" role="dialog" aria-modal="true"
                                aria-labelledby="guestDeleteTitle">
                                <div class="tms-modal-header">
                                    <h2 id="guestDeleteTitle">Hapus Tamu</h2>
                                    <button type="button" class="tms-modal-close" data-guest-delete-close
                                        aria-label="Tutup">&times;</button>
                                </div>
                                <form id="guestDeleteForm" method="POST" action="">
                                    @csrf
                                    @method('DELETE')
                                    <div class="tms-modal-body">
                                        <p>Hapus tamu <strong id="guestDeleteName"></strong> dari daftar tamu layanan ini?</p>
                                    </div>
                                    <div class="tms-modal-footer">
                                        <button type="button" class="tms-modal-btn tms-modal-btn-secondary"
                                            data-guest-delete-close>Batal</button>
                                        <button type="submit" class="tms-modal-btn tms-modal-btn-danger">Hapus Tamu</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- Itinerary --}}
                    <section class="service-show-panel">
                        <div class="service-show-panel-header">
                            <div class="service-show-panel-heading">
                                <div class="service-show-panel-icon service-show-orange">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 21s7-6.1 7-12a7 7 0 1 0-14 0c0 5.9 7 12 7 12Z" stroke="currentColor"
                                            stroke-width="1.7" />
                                        <circle cx="12" cy="9" r="2.5" stroke="currentColor"
                                            stroke-width="1.7" />
                                    </svg>
                                </div>
                                <div>
                                    <h2>Itinerary Perjalanan</h2>
                                    <p>Daftar destinasi dan status setiap pemberhentian.</p>
                                </div>
                            </div>

                            <div class="service-show-panel-actions">
                                <span class="service-show-count">
                                    {{ $service->stops->count() }} destinasi
                                </span>
                                @if ($canEditItinerary)
                                    <button type="button" class="service-show-action-add" data-stop-add>
                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8"
                                                stroke-linecap="round" />
                                        </svg>
                                        Tambah Destinasi
                                    </button>
                                @endif
                            </div>
                        </div>

                        @forelse ($service->stops as $stop)
                            <div class="service-show-stop">
                                <div class="service-show-stop-track">
                                    <span class="service-show-stop-number">{{ $stop->sort_order }}</span>
                                    @if (!$loop->last)
                                        <span class="service-show-stop-line"></span>
                                    @endif
                                </div>

                                <div class="service-show-stop-content">
                                    <div class="service-show-stop-heading">
                                        <h3>{{ $stop->name }}</h3>
                                        <div class="service-show-stop-meta">
                                            <div class="service-show-stop-badges">
                                                <span class="service-show-stop-status">
                                                    {{ $stop->status }}
                                                </span>

                                                @if ($stop->added_by_driver)
                                                    <span class="service-show-driver-added">
                                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                            <path d="M12 5v14M5 12h14" stroke="currentColor"
                                                                stroke-width="1.8" stroke-linecap="round" />
                                                        </svg>
                                                        Tambahan Driver
                                                    </span>
                                                @endif
                                            </div>
                                            @if ($canEditItinerary)
                                                <div class="service-show-stop-actions">
                                                    <button type="button" class="service-stop-btn service-stop-btn-edit"
                                                        data-stop-edit
                                                        data-id="{{ $stop->id }}"
                                                        data-action="{{ route('services.stops.update', [$service, $stop]) }}"
                                                        data-name="{{ $stop->name }}"
                                                        data-visit-time="{{ $stop->visit_time ? \Illuminate\Support\Carbon::parse($stop->visit_time)->format('H:i') : '' }}"
                                                        data-google-maps-url="{{ $stop->google_maps_url }}"
                                                        data-address="{{ $stop->address }}">
                                                        Edit
                                                    </button>
                                                    <button type="button" class="service-stop-btn service-stop-btn-delete"
                                                        data-stop-delete
                                                        data-name="{{ $stop->name }}"
                                                        data-action="{{ route('services.stops.destroy', [$service, $stop]) }}">
                                                        Hapus
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <p><strong>Waktu kunjungan:</strong>
                                        {{ $stop->visit_time ? \App\Support\DateFormatter::format($stop->visit_time, null, 'H:i') . ' WITA' : 'Belum ditentukan' }}
                                    </p>
                                    @if ($stop->google_maps_url)
                                        
                                            <a href="{{ $stop->google_maps_url }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="btn-map">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        width="18" height="18"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/>
                                                        <circle cx="12" cy="10" r="2.5"/>
                                                    </svg>

                                                    <span>Buka di Google Maps</span>

                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        width="14" height="14"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path d="M7 17 17 7"/>
                                                        <path d="M7 7h10v10"/>
                                                    </svg>
                                                </a>
                                    @endif
                                    <p>{{ $stop->address ?: 'Tidak ada keterangan lokasi.' }}</p>
                                    <span class="service-show-stop-sequence">
                                        DESTINASI {{ str_pad((string) $stop->sort_order, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="service-show-empty">
                                <div class="service-show-empty-icon">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 21s7-6.1 7-12a7 7 0 1 0-14 0c0 5.9 7 12 7 12Z" stroke="currentColor"
                                            stroke-width="1.7" />
                                        <circle cx="12" cy="9" r="2.5" stroke="currentColor"
                                            stroke-width="1.7" />
                                    </svg>
                                </div>
                                <h3>Belum ada destinasi</h3>
                                <p>Itinerary belum memiliki destinasi yang tercatat.</p>
                            </div>
                        @endforelse
                        @if ($isAdmin && ! $canEditItinerary)
                            <p class="service-guest-locked">
                                Layanan yang sudah berlalu atau selesai tidak dapat diubah lagi, termasuk itinerary.
                            </p>
                        @endif
                    </section>

                    @if ($canEditItinerary)
                        <div class="tms-modal" id="stopModal" hidden
                            data-open="{{ old('stop_context') ? '1' : '0' }}"
                            data-context="{{ old('stop_context', 'create') }}"
                            data-stop-id="{{ old('stop_id') }}"
                            data-edit-action="{{ old('stop_context') === 'edit' && old('stop_id') ? route('services.stops.update', [$service, old('stop_id')]) : '' }}">
                            <div class="tms-modal-backdrop" data-stop-modal-close></div>
                            <div class="tms-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="stopModalTitle">
                                <div class="tms-modal-header">
                                    <h2 id="stopModalTitle">Tambah Destinasi</h2>
                                    <button type="button" class="tms-modal-close" data-stop-modal-close
                                        aria-label="Tutup">&times;</button>
                                </div>
                                <form id="stopForm" method="POST" action="{{ route('services.stops.store', $service) }}">
                                    @csrf
                                    <input type="hidden" name="_method" id="stopFormMethod" value="POST">
                                    <input type="hidden" name="stop_context" id="stopContext" value="create">
                                    <input type="hidden" name="stop_id" id="stopId" value="">
                                    <div class="tms-modal-body">
                                        <div class="service-create-field">
                                            <label for="stopName">Nama destinasi <span>*</span></label>
                                            <input id="stopName" name="name" type="text" maxlength="150"
                                                value="{{ old('name') }}" required>
                                        </div>
                                        <div class="service-create-field">
                                            <label for="stopVisitTime">Waktu kunjungan</label>
                                            <input id="stopVisitTime" name="visit_time" type="time"
                                                value="{{ old('visit_time') }}">
                                        </div>
                                        <div class="service-create-field">
                                            <label for="stopMapsUrl">Short link Google Maps</label>
                                            <input id="stopMapsUrl" name="google_maps_url" type="url" maxlength="500"
                                                value="{{ old('google_maps_url') }}">
                                        </div>
                                        <div class="service-create-field">
                                            <label for="stopAddress">Alamat / keterangan lokasi</label>
                                            <input id="stopAddress" name="address" type="text" maxlength="500"
                                                value="{{ old('address') }}">
                                        </div>
                                    </div>
                                    <div class="tms-modal-footer">
                                        <button type="button" class="tms-modal-btn tms-modal-btn-secondary"
                                            data-stop-modal-close>Batal</button>
                                        <button type="submit" class="tms-modal-btn tms-modal-btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="tms-modal" id="stopDeleteModal" hidden>
                            <div class="tms-modal-backdrop" data-stop-delete-close></div>
                            <div class="tms-modal-dialog tms-modal-dialog-sm" role="dialog" aria-modal="true"
                                aria-labelledby="stopDeleteTitle">
                                <div class="tms-modal-header">
                                    <h2 id="stopDeleteTitle">Hapus Destinasi</h2>
                                    <button type="button" class="tms-modal-close" data-stop-delete-close
                                        aria-label="Tutup">&times;</button>
                                </div>
                                <form id="stopDeleteForm" method="POST" action="">
                                    @csrf
                                    @method('DELETE')
                                    <div class="tms-modal-body">
                                        <p>Hapus destinasi <strong id="stopDeleteName"></strong> dari itinerary layanan ini?</p>
                                    </div>
                                    <div class="tms-modal-footer">
                                        <button type="button" class="tms-modal-btn tms-modal-btn-secondary"
                                            data-stop-delete-close>Batal</button>
                                        <button type="submit" class="tms-modal-btn tms-modal-btn-danger">Hapus Destinasi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- Driver Activity Log --}}
                    <section class="service-show-panel service-show-activity-panel">
                        <div class="service-show-panel-header">
                            <div class="service-show-panel-heading">
                                <div class="service-show-panel-icon service-show-purple">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M4 19V5m0 14h16M8 15l3-4 3 2 5-7" stroke="currentColor" stroke-width="1.8"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div>
                                    <h2>Laporan Aktivitas Driver</h2>
                                    <p>Riwayat aktivitas dan data lokasi yang dikirim driver.</p>
                                </div>
                            </div>

                            <span class="service-show-count">
                                {{ $service->spk?->events?->count() ?? 0 }} aktivitas
                            </span>
                        </div>

                        <div class="service-show-table-scroll">
                            <table class="service-show-activity-table">
                                <thead>
                                    <tr>
                                        <th>Waktu Server (WITA)</th>
                                        <th>Aktivitas</th>
                                        <th>Destinasi</th>
                                        <th>Lokasi GPS</th>
                                        <th>Akurasi</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($service->spk?->events ?? [] as $e)
                                        <tr>
                                            <td>
                                                <div class="service-show-event-date">
                                                    {{ \App\Support\DateFormatter::format($e->created_at) ?? '-' }}
                                                </div>
                                                <div class="service-show-event-time">
                                                    {{ \App\Support\DateFormatter::format($e->created_at, null, 'H:i:s') ?? '-' }}
                                                </div>
                                            </td>

                                            <td>
                                                <span class="service-show-event-type">
                                                    {{ ucwords(str_replace('_', ' ', $e->event_type)) }}
                                                </span>
                                            </td>

                                            <td>{{ $e->stop?->name ?: '-' }}</td>

                                            <td>
                                                @if ($e->latitude !== null && $e->longitude !== null)
                                                    <a class="service-show-map-link" target="_blank"
                                                        rel="noopener noreferrer"
                                                        href="https://www.openstreetmap.org/?mlat={{ $e->latitude }}&mlon={{ $e->longitude }}#map=16/{{ $e->latitude }}/{{ $e->longitude }}">
                                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                            <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"
                                                                stroke="currentColor" stroke-width="1.7" />
                                                            <circle cx="12" cy="10" r="2.5"
                                                                stroke="currentColor" stroke-width="1.7" />
                                                        </svg>
                                                        Lihat Lokasi
                                                    </a>
                                                @else
                                                    <span class="service-show-no-location">Tidak tersedia</span>
                                                @endif
                                            </td>

                                            <td>
                                                {{ $e->accuracy_meters !== null ? number_format((float) $e->accuracy_meters, 0) . ' m' : '-' }}
                                            </td>

                                            <td class="service-show-event-note">
                                                {{ $e->note ?: '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">
                                                <div class="service-show-empty service-show-empty-compact">
                                                    <div class="service-show-empty-icon">
                                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                            <path d="M4 19V5m0 14h16M8 15l3-4 3 2 5-7" stroke="currentColor"
                                                                stroke-width="1.8" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                    </div>
                                                    <h3>Belum ada aktivitas driver</h3>
                                                    <p>Laporan aktivitas akan tampil di sini setelah driver mengirimkannya.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                <aside class="tms-info-panel" aria-label="Informasi Ringkasan SPK">
                    <div class="tms-info-panel-heading">
                        <span class="tms-info-panel-kicker">INFORMASI</span>
                        <h2>Ringkasan SPK</h2>
                        <p>Informasi penting untuk layanan yang sedang dibuka.</p>
                    </div>
                    <div class="tms-info-list">
                        <div class="tms-info-item">
                            <span class="tms-info-label">Status layanan</span>
                            <strong class="tms-info-value">{{ $service->status }}</strong>
                            <span class="tms-info-description">Status terkini pada layanan ini.</span>
                        </div>
                        <div class="tms-info-item">
                            <span class="tms-info-label">Tanggal mulai</span>
                            <strong
                                class="tms-info-value">{{ \App\Support\DateFormatter::format($service->service_date) ?? '-' }}</strong>
                            <span class="tms-info-description">Tanggal layanan.</span>
                        </div>
                        <div class="tms-info-item">
                            <span class="tms-info-label">Tanggal selesai</span>
                            <strong
                                class="tms-info-value">{{ \App\Support\DateFormatter::format($service->service_end_date) ?? '-' }}</strong>
                            <span class="tms-info-description">Tanggal akhir layanan.</span>
                        </div>
                        <div class="tms-info-item">
                            <span class="tms-info-label">Driver</span>
                            <strong class="tms-info-value">{{ $service->driver?->name ?? 'Belum ditugaskan' }}</strong>
                            <span class="tms-info-description">Driver yang ditetapkan.</span>
                        </div>
                        <div class="tms-info-item">
                            <span class="tms-info-label">Kendaraan</span>
                            <strong
                                class="tms-info-value">{{ $service->vehicle?->plate_number ?? 'Belum ditentukan' }}</strong>
                            <span class="tms-info-description">Nomor kendaraan.</span>
                        </div>
                        <div class="tms-info-item">
                            <span class="tms-info-label">Jumlah destinasi</span>
                            <strong class="tms-info-value">{{ $service->stops->count() }}</strong>
                            <span class="tms-info-description">Termasuk destinasi tambahan yang tersimpan.</span>
                        </div>
                        <div class="tms-info-item">
                            <span class="tms-info-label">Aktivitas SPK</span>
                            <strong class="tms-info-value">{{ $service->spk?->events?->count() ?? 0 }}</strong>
                            <span class="tms-info-description">Riwayat event SPK yang tersedia.</span>
                        </div>
                    </div>
                    <div class="tms-info-panel-footer">
                        <a href="{{ route('services.index') }}">&#8592; Kembali ke Daftar Layanan</a>
                    </div>
                </aside>
            </div>
        </div>

        @if ($newUrl)
            <script>
                (() => {
                    const sendForm = document.getElementById('sendSpkForm');
                    const sendButton = document.getElementById('sendSpkButton');
                    sendForm?.addEventListener('submit', () => {
                        sendButton.querySelector('span').textContent = 'Kirim Ulang SPK';
                    });
                    const sendInfoForm = document.getElementById('sendInfoForm');
                    const sendInfoButton = document.getElementById('sendInfoButton');
                    sendInfoForm?.addEventListener('submit', () => {
                        sendInfoButton.querySelector('span').textContent = 'Kirim Ulang Info';
                    });
                })();
            </script>
        @endif

        @if ($canEditGuests)
            <script>
                (() => {
                    const guestModal = document.getElementById('guestModal');
                    const deleteModal = document.getElementById('guestDeleteModal');
                    if (!guestModal || !deleteModal) return;

                    const form = document.getElementById('guestForm');
                    const methodInput = document.getElementById('guestFormMethod');
                    const contextInput = document.getElementById('guestContext');
                    const idInput = document.getElementById('guestId');
                    const title = document.getElementById('guestModalTitle');
                    const nameInput = document.getElementById('guestName');
                    const genderInput = document.getElementById('guestGender');
                    const ageInput = document.getElementById('guestAgeCategory');
                    const phoneInput = document.getElementById('guestPhone');
                    const deleteForm = document.getElementById('guestDeleteForm');
                    const deleteName = document.getElementById('guestDeleteName');

                    const storeAction = @json(route('services.guests.store', $service));

                    const syncBodyLock = () => {
                        document.body.classList.toggle('tms-modal-open', !guestModal.hidden || !deleteModal.hidden);
                    };

                    const openModal = (modal, focusEl) => {
                        modal.hidden = false;
                        syncBodyLock();
                        window.setTimeout(() => focusEl?.focus(), 30);
                    };

                    const closeModal = (modal) => {
                        modal.hidden = true;
                        syncBodyLock();
                    };

                    const fillForm = (data) => {
                        form.action = data.action;
                        methodInput.value = data.method;
                        contextInput.value = data.method === 'PUT' ? 'edit' : 'create';
                        idInput.value = data.id || '';
                        title.textContent = data.method === 'PUT' ? 'Edit Tamu' : 'Tambah Tamu';
                        nameInput.value = data.name || '';
                        genderInput.value = data.gender === 'Female' ? 'Female' : 'Male';
                        ageInput.value = data.ageCategory === 'Children' ? 'Children' : 'Adult';
                        phoneInput.value = data.phone || '';
                    };

                    document.querySelectorAll('[data-guest-add]').forEach((button) => {
                        button.addEventListener('click', () => {
                            fillForm({ action: storeAction, method: 'POST' });
                            openModal(guestModal, nameInput);
                        });
                    });

                    document.querySelectorAll('[data-guest-edit]').forEach((button) => {
                        button.addEventListener('click', () => {
                            fillForm({
                                action: button.dataset.action,
                                method: 'PUT',
                                id: button.dataset.id,
                                name: button.dataset.name,
                                gender: button.dataset.gender,
                                ageCategory: button.dataset.ageCategory,
                                phone: button.dataset.phone,
                            });
                            openModal(guestModal, nameInput);
                        });
                    });

                    document.querySelectorAll('[data-guest-delete]').forEach((button) => {
                        button.addEventListener('click', () => {
                            deleteForm.action = button.dataset.action;
                            deleteName.textContent = button.dataset.name || '';
                            openModal(deleteModal, deleteForm.querySelector('[type="submit"]'));
                        });
                    });

                    document.querySelectorAll('[data-guest-modal-close]').forEach((el) => {
                        el.addEventListener('click', () => closeModal(guestModal));
                    });

                    document.querySelectorAll('[data-guest-delete-close]').forEach((el) => {
                        el.addEventListener('click', () => closeModal(deleteModal));
                    });

                    document.addEventListener('keydown', (event) => {
                        if (event.key !== 'Escape') return;
                        if (!guestModal.hidden) closeModal(guestModal);
                        if (!deleteModal.hidden) closeModal(deleteModal);
                    });

                    if (guestModal.dataset.open === '1') {
                        if (guestModal.dataset.context === 'edit' && guestModal.dataset.editAction) {
                            form.action = guestModal.dataset.editAction;
                            methodInput.value = 'PUT';
                            contextInput.value = 'edit';
                            idInput.value = guestModal.dataset.guestId || '';
                            title.textContent = 'Edit Tamu';
                        }
                        openModal(guestModal, nameInput);
                    }
                })();
            </script>
        @endif

        @if ($canEditItinerary)
            <script>
                (() => {
                    const stopModal = document.getElementById('stopModal');
                    const deleteModal = document.getElementById('stopDeleteModal');
                    if (!stopModal || !deleteModal) return;

                    const form = document.getElementById('stopForm');
                    const methodInput = document.getElementById('stopFormMethod');
                    const contextInput = document.getElementById('stopContext');
                    const idInput = document.getElementById('stopId');
                    const title = document.getElementById('stopModalTitle');
                    const nameInput = document.getElementById('stopName');
                    const timeInput = document.getElementById('stopVisitTime');
                    const mapsInput = document.getElementById('stopMapsUrl');
                    const addressInput = document.getElementById('stopAddress');
                    const deleteForm = document.getElementById('stopDeleteForm');
                    const deleteName = document.getElementById('stopDeleteName');

                    const storeAction = @json(route('services.stops.store', $service));

                    const syncBodyLock = () => {
                        document.body.classList.toggle('tms-modal-open', !stopModal.hidden || !deleteModal.hidden);
                    };

                    const openModal = (modal, focusEl) => {
                        modal.hidden = false;
                        syncBodyLock();
                        window.setTimeout(() => focusEl?.focus(), 30);
                    };

                    const closeModal = (modal) => {
                        modal.hidden = true;
                        syncBodyLock();
                    };

                    const fillForm = (data) => {
                        form.action = data.action;
                        methodInput.value = data.method;
                        contextInput.value = data.method === 'PUT' ? 'edit' : 'create';
                        idInput.value = data.id || '';
                        title.textContent = data.method === 'PUT' ? 'Edit Destinasi' : 'Tambah Destinasi';
                        nameInput.value = data.name || '';
                        timeInput.value = data.visitTime || '';
                        mapsInput.value = data.googleMapsUrl || '';
                        addressInput.value = data.address || '';
                    };

                    document.querySelectorAll('[data-stop-add]').forEach((button) => {
                        button.addEventListener('click', () => {
                            fillForm({ action: storeAction, method: 'POST' });
                            openModal(stopModal, nameInput);
                        });
                    });

                    document.querySelectorAll('[data-stop-edit]').forEach((button) => {
                        button.addEventListener('click', () => {
                            fillForm({
                                action: button.dataset.action,
                                method: 'PUT',
                                id: button.dataset.id,
                                name: button.dataset.name,
                                visitTime: button.dataset.visitTime,
                                googleMapsUrl: button.dataset.googleMapsUrl,
                                address: button.dataset.address,
                            });
                            openModal(stopModal, nameInput);
                        });
                    });

                    document.querySelectorAll('[data-stop-delete]').forEach((button) => {
                        button.addEventListener('click', () => {
                            deleteForm.action = button.dataset.action;
                            deleteName.textContent = button.dataset.name || '';
                            openModal(deleteModal, deleteForm.querySelector('[type="submit"]'));
                        });
                    });

                    document.querySelectorAll('[data-stop-modal-close]').forEach((el) => {
                        el.addEventListener('click', () => closeModal(stopModal));
                    });

                    document.querySelectorAll('[data-stop-delete-close]').forEach((el) => {
                        el.addEventListener('click', () => closeModal(deleteModal));
                    });

                    document.addEventListener('keydown', (event) => {
                        if (event.key !== 'Escape') return;
                        if (!stopModal.hidden) closeModal(stopModal);
                        if (!deleteModal.hidden) closeModal(deleteModal);
                    });

                    if (stopModal.dataset.open === '1') {
                        if (stopModal.dataset.context === 'edit' && stopModal.dataset.editAction) {
                            form.action = stopModal.dataset.editAction;
                            methodInput.value = 'PUT';
                            contextInput.value = 'edit';
                            idInput.value = stopModal.dataset.stopId || '';
                            title.textContent = 'Edit Destinasi';
                        }
                        openModal(stopModal, nameInput);
                    }
                })();
            </script>
        @endif
    @endsection
