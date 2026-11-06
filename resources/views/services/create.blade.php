@extends('layouts.app')

@section('title', 'Buat SPK')

@section('content')
    @php
        $errorKeys = $errors->keys();
        $stepHasError = function (array $prefixes) use ($errorKeys) {
            foreach ($errorKeys as $key) {
                foreach ($prefixes as $prefix) {
                    if ($key === $prefix || str_starts_with($key, $prefix . '.')) {
                        return true;
                    }
                }
            }
            return false;
        };

        $initialStep = 1;
        if ($stepHasError(['service_date', 'service_end_date'])) {
            $initialStep = 2;
        } elseif ($stepHasError(['guests'])) {
            $initialStep = 3;
        } elseif ($stepHasError(['driver_id', 'vehicle_id', 'emergency_contact_id'])) {
            $initialStep = 4;
        } elseif ($stepHasError(['stops'])) {
            $initialStep = 5;
        }

        $oldGuests = array_values(array_filter((array) old('guests', []), 'is_array'));
        if (count($oldGuests) === 0) {
            $oldGuests = [[]];
        }

        $oldStops = array_values(array_filter((array) old('stops', []), 'is_array'));
        if (count($oldStops) === 0) {
            $oldStops = [[]];
        }

        $nextGuestIndex = count($oldGuests);
        $nextStopIndex = count($oldStops);
    @endphp
    <div class="service-create-page">
        {{-- Header --}}
        <div class="service-create-header">
            <div>
                <div class="service-create-eyebrow">
                    <span></span>
                    TRANSPORT OPERATIONS
                </div>
                <h1>Buat Layanan &amp; SPK</h1>
                <p>Buat penugasan transportasi dengan detail pelanggan, jadwal, driver, kendaraan, dan itinerary.</p>
            </div>

            <a href="{{ route('services.index') }}" class="service-create-back">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M19 12H5m7 7-7-7 7-7" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Kembali ke Layanan
            </a>
        </div>

        {{-- Information Banner --}}
        <div class="main-notice">
            <span class="main-notice-icon">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M12 11v5m0-8h.01" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round"/>
                </svg>
            </span>
            <div>
                <strong>Periksa detail sebelum menerbitkan SPK</strong>
                <p>Pastikan data layanan, jadwal, driver, kendaraan, dan destinasi sudah benar. SPK diterbitkan setelah validasi layanan berhasil.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('services.store') }}" class="service-create-form" id="serviceWizardForm"
            novalidate>
            @csrf

            {{-- Wizard Steps --}}
            <ol class="service-wizard-steps" aria-label="Langkah pembuatan layanan">
                @foreach ([
                    ['Detail Layanan', 'Pelanggan & tipe layanan'],
                    ['Jadwal Layanan', 'Tanggal & waktu'],
                    ['Daftar Tamu', 'Wisatawan'],
                    ['Penugasan Operasional', 'Driver & kendaraan'],
                    ['Itinerary', 'Destinasi perjalanan'],
                    ['Review & Terbitkan SPK', 'Ringkasan akhir'],
                ] as $index => $step)
                    <li class="service-wizard-step" data-wizard-indicator
                        aria-current="{{ $initialStep === $index + 1 ? 'step' : 'false' }}">
                        <span
                            class="service-wizard-step-index">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="service-wizard-step-text">
                            <strong>{{ $step[0] }}</strong>
                            <small>{{ $step[1] }}</small>
                        </span>
                    </li>
                @endforeach
            </ol>

            {{-- Step 1: Detail Layanan --}}
            <section class="service-create-card service-wizard-panel" data-wizard-panel
                @unless ($initialStep === 1) hidden @endunless>
                <div class="service-create-section-header">
                    <div class="service-create-section-icon service-icon-blue">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M16 20v-1.5a4.5 4.5 0 0 0-4.5-4.5h-3A4.5 4.5 0 0 0 4 18.5V20"
                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            <circle cx="10" cy="7" r="4" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M16 4.5a4 4 0 0 1 0 7.5m2 3a4.5 4.5 0 0 1 2 3.5V20"
                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Detail Layanan</h2>
                        <p>Informasi pelanggan dan kebutuhan transportasi.</p>
                    </div>
                    <span class="service-create-step">01</span>
                </div>

                <div class="service-create-fields">
                    <div class="service-create-field service-field-full">
                        <label for="user_id">Pemilik Reservasi / Kasus <span>*</span></label>
                        <select id="user_id" name="user_id" required>
                            <option value="">Pilih user ber-role reservation</option>
                            @foreach ($reservationUsers as $reservationUser)
                                <option value="{{ $reservationUser->id }}" @selected(old('user_id') == $reservationUser->id)>{{ $reservationUser->name }} ({{ $reservationUser->email }}{{ $reservationUser->phone ? ' · ' . $reservationUser->phone : '' }})</option>
                            @endforeach
                        </select>
                        @error('user_id')<small class="service-create-error">{{ $message }}</small>@enderror
                        <small class="service-create-help">Pemilik kasus adalah staf internal Reservation, bukan wisatawan.</small>
                    </div>
                    <div class="service-create-field">
                        <label for="customer_name">Nama pelanggan / kontak layanan <span>*</span></label>
                        <input id="customer_name" name="customer_name" type="text" value="{{ old('customer_name') }}"
                            placeholder="Nama pelanggan / kontak layanan" required>
                        @error('customer_name')<small class="service-create-error">{{ $message }}</small>@enderror
                    </div>

                    <div class="service-create-field">
                        <label for="customer_phone">Telepon Pelanggan</label>
                        <input id="customer_phone" name="customer_phone" type="tel"
                            value="{{ old('customer_phone') }}" placeholder="Contoh: +62 812..."
                            autocomplete="tel">
                        @error('customer_phone')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="service-create-field">
                        <label for="service_type_id">Tipe Layanan <span>*</span></label>
                        <select id="service_type_id" name="service_type_id" required>
                            <option value="">Pilih tipe layanan</option>
                            @foreach ($types as $t)
                                <option value="{{ $t->id }}" @selected(old('service_type_id') == $t->id)>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_type_id')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="service-create-field service-field-full">
                        <label for="notes">Catatan Layanan</label>
                        <textarea id="notes" name="notes" rows="3"
                            placeholder="Permintaan khusus, informasi tamu, atau instruksi tambahan...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Step 2: Jadwal Layanan --}}
            <section class="service-create-card service-wizard-panel" data-wizard-panel
                @unless ($initialStep === 2) hidden @endunless>
                <div class="service-create-section-header">
                    <div class="service-create-section-icon service-icon-purple">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="3.5" y="5" width="17" height="15.5" rx="2"
                                stroke="currentColor" stroke-width="1.7"/>
                            <path d="M7.5 3v4M16.5 3v4M3.5 9.5h17"
                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            <path d="m8.5 14 2.2 2.2 4.8-4.8" stroke="currentColor"
                                stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Jadwal Layanan</h2>
                        <p>Tentukan periode tanggal layanan. Waktu layanan mengikuti itinerary.</p>
                    </div>
                    <span class="service-create-step">02</span>
                </div>

                <div class="service-create-fields">
                    <div class="service-create-field">
                        <label for="service_date">Tanggal Mulai <span>*</span></label>
                        <input id="service_date" type="date" name="service_date"
                            min="{{ now('Asia/Makassar')->toDateString() }}"
                            value="{{ old('service_date', now('Asia/Makassar')->toDateString()) }}"
                            required>
                        @error('service_date')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="service-create-field">
                        <label for="service_end_date">Tanggal Akhir <span>*</span></label>
                        <input id="service_end_date" type="date" name="service_end_date"
                            min="{{ now('Asia/Makassar')->toDateString() }}"
                            value="{{ old('service_end_date', now('Asia/Makassar')->toDateString()) }}"
                            required>
                        @error('service_end_date')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
            </section>

            {{-- Step 3: Daftar Tamu --}}
            <section class="service-create-card service-wizard-panel" data-wizard-panel
                @unless ($initialStep === 3) hidden @endunless>
                <div class="service-create-section-header">
                    <div class="service-create-section-icon service-icon-blue">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="9" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M2.5 20v-1.5a6.5 6.5 0 0 1 13 0V20"
                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            <path d="M16 5.2a3.5 3.5 0 0 1 0 6.6M18 14a5.5 5.5 0 0 1 3.5 5.1V20"
                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Daftar Tamu / Wisatawan</h2>
                        <p>Pemilik Reservasi tidak otomatis menjadi tamu. Boleh dikosongkan bila data belum tersedia.</p>
                    </div>
                    <span class="service-create-step">03</span>
                </div>

                <div id="service-guests" class="service-stops">
                    @foreach ($oldGuests as $i => $guest)
                        <div class="service-stop-row">
                            <div class="service-stop-number">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="service-stop-fields">
                                <div class="service-create-field">
                                    <label>Nama tamu</label>
                                    <input name="guests[{{ $i }}][name]" type="text" maxlength="150"
                                        value="{{ $guest['name'] ?? '' }}" placeholder="Nama tamu">
                                </div>
                                <div class="service-create-field">
                                    <label>Jenis kelamin</label>
                                    <select name="guests[{{ $i }}][gender]">
                                        <option value="">Pilih</option>
                                        <option value="Male" @selected(($guest['gender'] ?? '') === 'Male')>Male</option>
                                        <option value="Female" @selected(($guest['gender'] ?? '') === 'Female')>Female</option>
                                    </select>
                                </div>
                                <div class="service-create-field">
                                    <label>Kategori usia</label>
                                    <select name="guests[{{ $i }}][age_category]">
                                        <option value="">Pilih</option>
                                        <option value="Adult" @selected(($guest['age_category'] ?? '') === 'Adult')>Adult</option>
                                        <option value="Children" @selected(($guest['age_category'] ?? '') === 'Children')>Children</option>
                                    </select>
                                </div>
                                <div class="service-create-field">
                                    <label>Kontak (opsional)</label>
                                    <input name="guests[{{ $i }}][phone]" type="tel" maxlength="40"
                                        value="{{ $guest['phone'] ?? '' }}" placeholder="Nomor telepon tamu">
                                </div>
                            </div>
                            <button type="button" class="service-remove-stop" aria-label="Hapus tamu">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 7h16M9 7V5h6v2m-8 0 1 13h8l1-13" stroke="currentColor"
                                        stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                <button class="service-add-stop-btn" type="button" id="addGuest">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    Tambah Tamu
                </button>
            </section>

            {{-- Step 4: Penugasan Operasional --}}
            <section class="service-create-card service-wizard-panel" data-wizard-panel
                @unless ($initialStep === 4) hidden @endunless>
                <div class="service-create-section-header">
                    <div class="service-create-section-icon service-icon-green">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m5 11 2-5h10l2 5M4 11h16v7H4z"
                                stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            <path d="M7 18v2m10-2v2M4 14h3m10 0h3"
                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            <circle cx="7.5" cy="14.5" r=".8" fill="currentColor"/>
                            <circle cx="16.5" cy="14.5" r=".8" fill="currentColor"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Penugasan Operasional</h2>
                        <p>Pilih driver, kendaraan, dan kontak darurat.</p>
                    </div>
                    <span class="service-create-step">04</span>
                </div>

                <div class="service-create-fields">
                    <div class="service-create-field">
                        <label for="driver_id">Driver <span>*</span></label>
                        <select id="driver_id" name="driver_id" required>
                            <option value="">Pilih driver</option>
                            @foreach ($drivers as $d)
                                <option value="{{ $d->id }}" @selected(old('driver_id') == $d->id)>
                                    {{ $d->name }}{{ $d->phone ? ' — ' . $d->phone : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('driver_id')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="service-create-field">
                        <label for="vehicle_id">Kendaraan <span>*</span></label>
                        <select id="vehicle_id" name="vehicle_id" required>
                            <option value="">Pilih kendaraan</option>
                            @foreach ($vehicles as $v)
                                <option value="{{ $v->id }}" @selected(old('vehicle_id') == $v->id)>
                                    {{ $v->name }} — {{ $v->plate_number }} ({{ $v->capacity }} pax)
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="service-create-field service-field-full">
                        <label for="emergency_contact_id">Kontak Darurat</label>
                        <select id="emergency_contact_id" name="emergency_contact_id">
                            <option value="">Gunakan kontak default</option>
                            @foreach ($contacts as $c)
                                <option value="{{ $c->id }}"
                                    @selected(old('emergency_contact_id') == $c->id)>
                                    {{ $c->name }} — {{ $c->phone }}
                                </option>
                            @endforeach
                        </select>
                        <small class="service-create-help">
                            Kontak default digunakan jika tidak memilih kontak darurat khusus.
                        </small>
                        @error('emergency_contact_id')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Step 5: Itinerary --}}
            <section class="service-create-card service-wizard-panel" data-wizard-panel
                @unless ($initialStep === 5) hidden @endunless>
                <div class="service-create-section-header">
                    <div class="service-create-section-icon service-icon-orange">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 21s7-6.1 7-12a7 7 0 1 0-14 0c0 5.9 7 12 7 12Z"
                                stroke="currentColor" stroke-width="1.7"/>
                            <circle cx="12" cy="9" r="2.5" stroke="currentColor" stroke-width="1.7"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Itinerary Awal</h2>
                        <p>Susun destinasi yang direncanakan selama layanan berlangsung.</p>
                    </div>
                    <span class="service-create-step">05</span>
                </div>

                <div class="service-itinerary-info">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/>
                        <path d="M12 11v5m0-8h.01" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    Driver dapat menambah atau membatalkan destinasi selama SPK aktif.
                    Maksimal 40 destinasi awal.
                </div>

                <div id="stops" class="service-stops">
                    @foreach ($oldStops as $i => $stop)
                        <div class="service-stop-row">
                            <div class="service-stop-number">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="service-stop-fields">
                                <div class="service-create-field">
                                    <label for="stop-name-{{ $i }}">Nama Destinasi <span>*</span></label>
                                    <input id="stop-name-{{ $i }}" name="stops[{{ $i }}][name]" type="text"
                                        value="{{ $stop['name'] ?? '' }}" required
                                        placeholder="Contoh: Uluwatu Temple">
                                </div>
                                <div class="service-create-field">
                                    <label for="stop-time-{{ $i }}">Waktu Kunjungan <span>*</span></label>
                                    <input id="stop-time-{{ $i }}" name="stops[{{ $i }}][visit_time]" type="time"
                                        value="{{ $stop['visit_time'] ?? '' }}" required>
                                </div>
                                <div class="service-create-field">
                                    <label for="stop-map-{{ $i }}">Short Link Google Maps <span>*</span></label>
                                    <input id="stop-map-{{ $i }}" name="stops[{{ $i }}][google_maps_url]" type="url"
                                        value="{{ $stop['google_maps_url'] ?? '' }}" required
                                        placeholder="https://maps.app.goo.gl/...">
                                </div>
                                <div class="service-create-field">
                                    <label for="stop-address-{{ $i }}">Alamat / Keterangan Lokasi</label>
                                    <input id="stop-address-{{ $i }}" name="stops[{{ $i }}][address]" type="text"
                                        value="{{ $stop['address'] ?? '' }}"
                                        placeholder="Alamat atau area destinasi">
                                </div>
                            </div>
                            @if (count($oldStops) > 1)
                                <button type="button" class="service-remove-stop" aria-label="Hapus destinasi">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M4 7h16M9 7V5h6v2m-8 0 1 13h8l1-13" stroke="currentColor"
                                            stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>

                <button class="service-add-stop-btn" type="button" id="addStop">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    Tambah Destinasi
                </button>
                <p class="service-stop-counter" id="stopCounter">1 dari 40 destinasi</p>
            </section>

            {{-- Step 6: Review & Terbitkan --}}
            <section class="service-create-card service-wizard-panel" data-wizard-panel
                @unless ($initialStep === 6) hidden @endunless>
                <div class="service-create-section-header">
                    <div class="service-create-section-icon service-icon-blue">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12.5 9.5 17 19 7" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Review &amp; Terbitkan SPK</h2>
                        <p>Periksa kembali seluruh data sebelum layanan dan SPK diterbitkan.</p>
                    </div>
                    <span class="service-create-step">06</span>
                </div>

                <div class="service-wizard-review-notice">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/>
                        <path d="M12 11v5m0-8h.01" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round"/>
                    </svg>
                    <p>Setelah tombol <strong>Terbitkan SPK</strong> ditekan, layanan akan disimpan dan SPK otomatis dibuat.
                        Pastikan data sudah benar karena SPK langsung dapat dibagikan.</p>
                </div>

                <div id="wizardReview" class="service-wizard-review"></div>
            </section>

            {{-- Wizard Footer --}}
            <div class="service-wizard-footer">
                <a href="{{ route('services.index') }}" class="service-cancel-btn">
                    Batal
                </a>
                <div class="service-wizard-footer-actions">
                    <button type="button" class="service-wizard-btn service-wizard-btn-secondary"
                        data-wizard-prev hidden>
                        Sebelumnya
                    </button>
                    <button type="button" class="service-wizard-btn service-wizard-btn-primary"
                        data-wizard-next>
                        Selanjutnya
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <button type="submit" class="service-submit-btn" data-wizard-submit hidden>
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12.5 9.5 17 19 7" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Terbitkan SPK
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        (() => {
            const form = document.getElementById('serviceWizardForm');
            if (!form) return;

            const panels = Array.from(form.querySelectorAll('[data-wizard-panel]'));
            const indicators = Array.from(document.querySelectorAll('[data-wizard-indicator]'));
            const prevButton = form.querySelector('[data-wizard-prev]');
            const nextButton = form.querySelector('[data-wizard-next]');
            const submitButton = form.querySelector('[data-wizard-submit]');
            const reviewRoot = document.getElementById('wizardReview');
            const guestContainer = document.getElementById('service-guests');
            const stopContainer = document.getElementById('stops');
            const stopCounter = document.getElementById('stopCounter');
            const serviceDate = document.getElementById('service_date');
            const serviceEndDate = document.getElementById('service_end_date');
            const maxStops = 40;
            let currentStep = Math.min(Math.max({{ $initialStep }} - 1, 0), panels.length - 1);
            let nextGuestIndex = {{ $nextGuestIndex }};
            let nextStopIndex = {{ $nextStopIndex }};

            const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
            }[character]));

            const panelFields = (panel) => Array.from(panel.querySelectorAll('input, select, textarea'))
                .filter((field) => !field.disabled);

            const firstInvalidField = (panel) => panelFields(panel).find((field) => !field.checkValidity()) || null;

            const setStep = (index, scroll = true) => {
                currentStep = Math.max(0, Math.min(index, panels.length - 1));

                panels.forEach((panel, position) => {
                    panel.hidden = position !== currentStep;
                });

                indicators.forEach((indicator, position) => {
                    indicator.classList.toggle('is-active', position === currentStep);
                    indicator.classList.toggle('is-complete', position < currentStep);
                    indicator.setAttribute('aria-current', position === currentStep ? 'step' : 'false');
                });

                prevButton.hidden = currentStep === 0;
                nextButton.hidden = currentStep === panels.length - 1;
                submitButton.hidden = currentStep !== panels.length - 1;

                if (currentStep === panels.length - 1) buildReview();

                if (scroll) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            };

            const validatePanel = (panel) => {
                const invalid = firstInvalidField(panel);
                if (invalid) {
                    invalid.reportValidity();
                    invalid.focus();
                    return false;
                }
                return true;
            };

            const renumber = (container) => {
                container?.querySelectorAll('.service-stop-row').forEach((row, position) => {
                    const number = row.querySelector('.service-stop-number');
                    if (number) number.textContent = String(position + 1).padStart(2, '0');
                });
            };

            const updateStopCounter = () => {
                if (!stopContainer || !stopCounter) return;
                const total = stopContainer.querySelectorAll('.service-stop-row').length;
                stopCounter.textContent = `${total} dari ${maxStops} destinasi`;
                const addStop = document.getElementById('addStop');
                if (addStop) addStop.disabled = total >= maxStops;
            };

            const removeIcon = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M9 7V5h6v2m-8 0 1 13h8l1-13" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /></svg>';

            guestContainer?.addEventListener('click', (event) => {
                const button = event.target.closest('.service-remove-stop');
                if (!button) return;
                button.closest('.service-stop-row')?.remove();
                renumber(guestContainer);
            });

            stopContainer?.addEventListener('click', (event) => {
                const button = event.target.closest('.service-remove-stop');
                if (!button || stopContainer.querySelectorAll('.service-stop-row').length <= 1) return;
                button.closest('.service-stop-row')?.remove();
                renumber(stopContainer);
                updateStopCounter();
            });

            document.getElementById('addGuest')?.addEventListener('click', () => {
                if (!guestContainer || guestContainer.querySelectorAll('.service-stop-row').length >= 100) return;
                const i = nextGuestIndex++;
                const row = document.createElement('div');
                row.className = 'service-stop-row';
                row.innerHTML = `
                    <div class="service-stop-number"></div>
                    <div class="service-stop-fields">
                        <div class="service-create-field">
                            <label>Nama tamu</label>
                            <input name="guests[${i}][name]" type="text" maxlength="150" placeholder="Nama tamu">
                        </div>
                        <div class="service-create-field">
                            <label>Jenis kelamin</label>
                            <select name="guests[${i}][gender]">
                                <option value="">Pilih</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="service-create-field">
                            <label>Kategori usia</label>
                            <select name="guests[${i}][age_category]">
                                <option value="">Pilih</option>
                                <option value="Adult">Adult</option>
                                <option value="Children">Children</option>
                            </select>
                        </div>
                        <div class="service-create-field">
                            <label>Kontak (opsional)</label>
                            <input name="guests[${i}][phone]" type="tel" maxlength="40" placeholder="Nomor telepon tamu">
                        </div>
                    </div>
                    <button type="button" class="service-remove-stop" aria-label="Hapus tamu">${removeIcon}</button>
                `;
                guestContainer.appendChild(row);
                renumber(guestContainer);
            });

            document.getElementById('addStop')?.addEventListener('click', () => {
                if (!stopContainer || stopContainer.querySelectorAll('.service-stop-row').length >= maxStops) return;
                const i = nextStopIndex++;
                const row = document.createElement('div');
                row.className = 'service-stop-row';
                row.innerHTML = `
                    <div class="service-stop-number"></div>
                    <div class="service-stop-fields">
                        <div class="service-create-field">
                            <label>Nama Destinasi <span>*</span></label>
                            <input name="stops[${i}][name]" type="text" required placeholder="Contoh: Uluwatu Temple">
                        </div>
                        <div class="service-create-field">
                            <label>Waktu Kunjungan <span>*</span></label>
                            <input name="stops[${i}][visit_time]" type="time" required>
                        </div>
                        <div class="service-create-field">
                            <label>Short Link Google Maps <span>*</span></label>
                            <input name="stops[${i}][google_maps_url]" type="url" required placeholder="https://maps.app.goo.gl/...">
                        </div>
                        <div class="service-create-field">
                            <label>Alamat / Keterangan Lokasi</label>
                            <input name="stops[${i}][address]" type="text" placeholder="Alamat atau area destinasi">
                        </div>
                    </div>
                    <button type="button" class="service-remove-stop" aria-label="Hapus destinasi">${removeIcon}</button>
                `;
                stopContainer.appendChild(row);
                renumber(stopContainer);
                updateStopCounter();
            });

            const syncEndDateMinimum = () => {
                if (!serviceDate || !serviceEndDate || !serviceDate.value) return;
                serviceEndDate.min = serviceDate.value;
                if (serviceEndDate.value && serviceEndDate.value < serviceDate.value) {
                    serviceEndDate.value = serviceDate.value;
                }
            };

            serviceDate?.addEventListener('change', syncEndDateMinimum);

            const selectedText = (id, fallback = '—') => {
                const select = document.getElementById(id);
                if (!select || select.selectedIndex < 0) return fallback;
                return select.options[select.selectedIndex]?.textContent?.trim() || fallback;
            };

            const fieldValue = (id) => document.getElementById(id)?.value?.trim() || '';

            const reviewItems = (items) => `
                <div class="service-wizard-review-grid">
                    ${items.map(([label, value]) => `
                        <div class="service-wizard-review-item">
                            <span class="service-wizard-review-label">${escapeHtml(label)}</span>
                            <strong class="service-wizard-review-value">${escapeHtml(value || '—')}</strong>
                        </div>
                    `).join('')}
                </div>
            `;

            const reviewSection = (title, items, extra = '') => `
                <div class="service-wizard-review-section">
                    <h3>${escapeHtml(title)}</h3>
                    ${reviewItems(items)}
                    ${extra}
                </div>
            `;

            const buildReview = () => {
                if (!reviewRoot) return;

                const guests = Array.from(guestContainer?.querySelectorAll('.service-stop-row') || [])
                    .map((row) => {
                        const name = row.querySelector('input[name*="[name]"]')?.value.trim();
                        if (!name) return null;
                        const gender = row.querySelector('select[name*="[gender]"]')?.value || '';
                        const age = row.querySelector('select[name*="[age_category]"]')?.value || '';
                        const phone = row.querySelector('input[name*="[phone]"]')?.value.trim() || '';
                        return { name, gender, age, phone };
                    })
                    .filter(Boolean);

                const stops = Array.from(stopContainer?.querySelectorAll('.service-stop-row') || [])
                    .map((row) => {
                        const name = row.querySelector('input[name*="[name]"]')?.value.trim();
                        if (!name) return null;
                        const time = row.querySelector('input[name*="[visit_time]"]')?.value || '';
                        const map = row.querySelector('input[name*="[google_maps_url]"]')?.value.trim() || '';
                        const address = row.querySelector('input[name*="[address]"]')?.value.trim() || '';
                        return { name, time, map, address };
                    })
                    .filter(Boolean);

                const guestList = guests.length
                    ? `<ul class="service-wizard-review-list">${guests.map((guest) => `<li>${escapeHtml(guest.name)}${guest.gender || guest.age ? ` — ${escapeHtml([guest.gender, guest.age].filter(Boolean).join(' / '))}` : ''}${guest.phone ? ` — ${escapeHtml(guest.phone)}` : ''}</li>`).join('')}</ul>`
                    : '<p class="service-wizard-review-empty">Belum ada tamu pada daftar.</p>';

                const stopList = stops.length
                    ? `<ul class="service-wizard-review-list">${stops.map((stop) => `<li><strong>${escapeHtml(stop.time || '--:--')}</strong> — ${escapeHtml(stop.name)}${stop.address ? ` (${escapeHtml(stop.address)})` : ''}</li>`).join('')}</ul>`
                    : '<p class="service-wizard-review-empty">Belum ada destinasi pada itinerary.</p>';

                reviewRoot.innerHTML = [
                    reviewSection('Detail Layanan', [
                        ['Pemilik Reservation', selectedText('user_id', 'Belum dipilih')],
                        ['Pelanggan', fieldValue('customer_name')],
                        ['Telepon', fieldValue('customer_phone')],
                        ['Tipe Layanan', selectedText('service_type_id', 'Belum dipilih')],
                        ['Catatan', fieldValue('notes')],
                    ]),
                    reviewSection('Jadwal Layanan', [
                        ['Tanggal Mulai', fieldValue('service_date')],
                        ['Tanggal Akhir', fieldValue('service_end_date')],
                    ]),
                    reviewSection('Daftar Tamu', [
                        ['Jumlah Tamu', guests.length ? `${guests.length} orang` : 'Belum ada tamu'],
                    ], guestList),
                    reviewSection('Penugasan Operasional', [
                        ['Driver', selectedText('driver_id', 'Belum dipilih')],
                        ['Kendaraan', selectedText('vehicle_id', 'Belum dipilih')],
                        ['Kontak Darurat', selectedText('emergency_contact_id', 'Gunakan kontak default')],
                    ]),
                    reviewSection('Itinerary', [
                        ['Jumlah Destinasi', stops.length ? `${stops.length} destinasi` : 'Belum ada destinasi'],
                    ], stopList),
                ].join('');
            };

            nextButton?.addEventListener('click', () => {
                if (!validatePanel(panels[currentStep])) return;
                setStep(currentStep + 1);
            });

            prevButton?.addEventListener('click', () => setStep(currentStep - 1));

            form.addEventListener('submit', (event) => {
                for (let index = 0; index < panels.length - 1; index += 1) {
                    const invalid = firstInvalidField(panels[index]);
                    if (invalid) {
                        event.preventDefault();
                        setStep(index);
                        window.setTimeout(() => {
                            invalid.reportValidity();
                            invalid.focus();
                        }, 80);
                        return;
                    }
                }
            });

            renumber(guestContainer);
            updateStopCounter();
            syncEndDateMinimum();
            setStep(currentStep, false);
        })();
    </script>
@endsection
