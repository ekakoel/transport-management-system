@extends('layouts.app')

@section('title', 'Buat Permintaan Transportasi')

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

        $stepErrorMap = [
            1 => ['customer_name', 'order_number', 'guest_count', 'service_type_id', 'service_date', 'service_end_date'],
            2 => ['guests'],
            3 => ['itinerary_stops'],
            4 => ['notes'],
        ];

        $initialStep = 1;
        foreach ($stepErrorMap as $step => $prefixes) {
            if ($stepHasError($prefixes)) {
                $initialStep = $step;
                break;
            }
        }

        $oldGuests = array_filter((array) old('guests', []), 'is_array');
        if (count($oldGuests) === 0) {
            $oldGuests = [0 => ['name' => '', 'gender' => '', 'age_category' => '', 'phone' => '']];
        }
        $nextGuestIndex = ((int) max(array_keys($oldGuests))) + 1;

        $oldStops = array_filter((array) old('itinerary_stops', []), 'is_array');
        if (count($oldStops) === 0) {
            $oldStops = [0 => ['name' => '', 'visit_time' => '', 'google_maps_url' => '', 'note' => '']];
        }
        $nextStopIndex = ((int) max(array_keys($oldStops))) + 1;
    @endphp

    <div class="service-create-page">
        {{-- Header --}}
        <div class="service-create-header">
            <div>
                <div class="service-create-eyebrow">
                    <span></span>
                    RESERVASI TRANSPORTASI
                </div>
                <h1>Buat Permintaan Transportasi</h1>
                <p>Permintaan akan berstatus Pending sampai ditinjau admin. SPK baru dibuat setelah disetujui.</p>
            </div>

            <a href="{{ route('reservations.index') }}" class="service-create-back">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M19 12H5m7 7-7-7 7-7" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Kembali ke Reservasi
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
                <strong>Periksa detail sebelum melakukan permintaan layanan</strong>
                <p>Pastikan data layanan, jadwal, dan destinasi sudah benar. Permintaan layanan dikirimkan setelah validasi permintaan layanan berhasil.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('reservations.store') }}" class="service-create-form"
            id="reservationWizardForm" novalidate>
            @csrf

            {{-- Wizard Steps --}}
            <ol class="service-wizard-steps service-wizard-steps--5" aria-label="Langkah pembuatan permintaan layanan">
                @foreach ([
                    ['Detail Layanan', 'Pelanggan & jadwal'],
                    ['Daftar Tamu', 'Data wisatawan'],
                    ['Itinerary', 'Destinasi perjalanan'],
                    ['Note', 'Catatan untuk admin'],
                    ['Review input & Submit', 'Ringkasan akhir'],
                ] as $index => $step)
                    <li class="service-wizard-step" data-wizard-indicator
                        aria-current="{{ $initialStep === $index + 1 ? 'step' : 'false' }}">
                        <span class="service-wizard-step-index">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
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
                    <div class="service-create-section-icon service-icon-green">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="5" y="4" width="14" height="17" rx="2.5" stroke="currentColor"
                                stroke-width="1.7" />
                            <path d="M9 3h6v3H9z" stroke="currentColor" stroke-width="1.7"
                                stroke-linejoin="round" />
                            <path d="m8.5 13 2 2 4-4" stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div>
                        <h2>Detail Layanan</h2>
                        <p>Informasi pelanggan dan kebutuhan transportasi.</p>
                    </div>
                    <span class="service-create-step">01</span>
                </div>

                <div class="service-create-fields">
                    <div class="service-create-field">
                        <label for="customer_name">Nama pelanggan / Nama Agent <span>*</span></label>
                        <input id="customer_name" name="customer_name" type="text" maxlength="150"
                            value="{{ old('customer_name', auth()->user()->name) }}"
                            placeholder="Nama pelanggan / Nama Agent" required>
                        @error('customer_name')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="service-create-field">
                        <label for="order_number">Nomor Order</label>
                        <input id="order_number" name="order_number" type="text" maxlength="100"
                            value="{{ old('order_number') }}"
                            placeholder="Nomor order dari pelanggan / sistem">
                        <small class="service-create-help">Opsional. Admin juga dapat mengisinya saat SPK diterbitkan.</small>
                        @error('order_number')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="service-create-field">
                        <label for="guest_count">Jumlah Tamu <span>*</span></label>
                        <input id="guest_count" name="guest_count" type="number" min="1" max="100"
                            value="{{ old('guest_count', 1) }}" required>
                        <small class="service-create-help">Otomatis mengikuti daftar tamu bila daftar tamu diisi.</small>
                        @error('guest_count')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="service-create-field">
                        <label for="service_type_id">Tipe Layanan <span>*</span></label>
                        <select id="service_type_id" name="service_type_id" required>
                            <option value="">Pilih tipe layanan</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" @selected(old('service_type_id') == $type->id)>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('service_type_id')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <br>

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

            {{-- Step 2: Daftar Tamu --}}
            <section class="service-create-card service-wizard-panel" data-wizard-panel
                @unless ($initialStep === 2) hidden @endunless>
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
                        <h2>Daftar Tamu</h2>
                        <p>Tambahkan daftar tamu sesuai dengan jumlah pax.</p>
                    </div>
                    <span class="service-create-step">02</span>
                </div>

                @error('guests')
                    <small class="service-create-error">{{ $message }}</small>
                @enderror

                <div id="reservation-guests" class="service-stops">
                    @foreach ($oldGuests as $index => $guest)
                        <div class="service-stop-row">
                            <div class="service-stop-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="service-stop-fields">
                                <div class="service-create-field">
                                    <label>Nama tamu <span>*</span></label>
                                    <input name="guests[{{ $index }}][name]" type="text" maxlength="150"
                                        value="{{ $guest['name'] ?? '' }}" placeholder="Nama tamu" required>
                                    @error('guests.'.$index.'.name')
                                        <small class="service-create-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="service-create-field">
                                    <label>Jenis kelamin <span>*</span></label>
                                    <select name="guests[{{ $index }}][gender]" required>
                                        <option value="">Pilih</option>
                                        <option value="Male" @selected(($guest['gender'] ?? '') === 'Male')>Male (Pria)</option>
                                        <option value="Female" @selected(($guest['gender'] ?? '') === 'Female')>Female (Wanita)</option>
                                    </select>
                                    @error('guests.'.$index.'.gender')
                                        <small class="service-create-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="service-create-field">
                                    <label>Kategori usia <span>*</span></label>
                                    <select name="guests[{{ $index }}][age_category]" required>
                                        <option value="">Pilih</option>
                                        <option value="Adult" @selected(($guest['age_category'] ?? '') === 'Adult')>Adult (Dewasa)</option>
                                        <option value="Children" @selected(($guest['age_category'] ?? '') === 'Children')>Children (Anak-anak)</option>
                                    </select>
                                    @error('guests.'.$index.'.age_category')
                                        <small class="service-create-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="service-create-field">
                                    <label>Kontak (opsional)</label>
                                    <input name="guests[{{ $index }}][phone]" type="tel" maxlength="40"
                                        value="{{ $guest['phone'] ?? '' }}" placeholder="Nomor telepon tamu">
                                    @error('guests.'.$index.'.phone')
                                        <small class="service-create-error">{{ $message }}</small>
                                    @enderror
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
                <p class="service-stop-counter" id="guestCounter">{{ count($oldGuests) }} dari 100 tamu</p>
            </section>

            {{-- Step 3: Itinerary --}}
            <section class="service-create-card service-wizard-panel" data-wizard-panel
                @unless ($initialStep === 3) hidden @endunless>
                <div class="service-create-section-header">
                    <div class="service-create-section-icon service-icon-orange">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 21s7-6.1 7-12a7 7 0 1 0-14 0c0 5.9 7 12 7 12Z"
                                stroke="currentColor" stroke-width="1.7"/>
                            <circle cx="12" cy="9" r="2.5" stroke="currentColor" stroke-width="1.7"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Itinerary</h2>
                        <p>Susun destinasi yang direncanakan selama layanan berlangsung.</p>
                    </div>
                    <span class="service-create-step">03</span>
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

                @error('itinerary_stops')
                    <small class="service-create-error">{{ $message }}</small>
                @enderror

                <div id="reservation-stops" class="service-stops">
                    @foreach ($oldStops as $index => $stop)
                        <div class="service-stop-row">
                            <div class="service-stop-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="service-stop-fields">
                                <div class="service-create-field">
                                    <label>Nama destinasi <span>*</span></label>
                                    <input name="itinerary_stops[{{ $index }}][name]" type="text" maxlength="150"
                                        value="{{ $stop['name'] ?? '' }}" placeholder="Contoh: Uluwatu Temple" required>
                                    @error('itinerary_stops.'.$index.'.name')
                                        <small class="service-create-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="service-create-field">
                                    <label>Waktu kunjungan <span>*</span></label>
                                    <input name="itinerary_stops[{{ $index }}][visit_time]" type="time"
                                        value="{{ substr((string) ($stop['visit_time'] ?? ''), 0, 5) }}" required>
                                    @error('itinerary_stops.'.$index.'.visit_time')
                                        <small class="service-create-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="service-create-field">
                                    <label>Short link Google Maps <span>*</span></label>
                                    <input name="itinerary_stops[{{ $index }}][google_maps_url]" type="url"
                                        maxlength="500" value="{{ $stop['google_maps_url'] ?? '' }}"
                                        placeholder="https://maps.app.goo.gl/..." required>
                                    @error('itinerary_stops.'.$index.'.google_maps_url')
                                        <small class="service-create-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="service-create-field">
                                    <label>Keterangan</label>
                                    <input name="itinerary_stops[{{ $index }}][note]" type="text" maxlength="500"
                                        value="{{ $stop['note'] ?? '' }}" placeholder="Keterangan destinasi (opsional)">
                                    @error('itinerary_stops.'.$index.'.note')
                                        <small class="service-create-error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <button type="button" class="service-remove-stop" aria-label="Hapus destinasi">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 7h16M9 7V5h6v2m-8 0 1 13h8l1-13" stroke="currentColor"
                                        stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
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
                <p class="service-stop-counter" id="stopCounter">{{ count($oldStops) }} dari 40 destinasi</p>
            </section>

            {{-- Step 4: Note --}}
            <section class="service-create-card service-wizard-panel" data-wizard-panel
                @unless ($initialStep === 4) hidden @endunless>
                <div class="service-create-section-header">
                    <div class="service-create-section-icon service-icon-blue">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 3.5h7l5 5V14" stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14 3.5v5h5" stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 3.5H5a1.5 1.5 0 0 0-1.5 1.5v14A1.5 1.5 0 0 0 5 20.5h8"
                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 12h7M7 15.5h4" stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round"/>
                            <circle cx="17.5" cy="17.5" r="3.5" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M17.5 15.8v3.4M15.8 17.5h3.4" stroke="currentColor"
                                stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Note</h2>
                        <p>Tambahkan note untuk admin.</p>
                    </div>
                    <span class="service-create-step">04</span>
                </div>

                <div class="service-create-fields">
                    <div class="service-create-field service-field-full">
                        <label for="notes">Catatan / kebutuhan khusus</label>
                        <textarea id="notes" name="notes" rows="4"
                            placeholder="Permintaan khusus, informasi tamu, atau instruksi tambahan...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Step 5: Review & Submit --}}
            <section class="service-create-card service-wizard-panel" data-wizard-panel
                @unless ($initialStep === 5) hidden @endunless>
                <div class="service-create-section-header">
                    <div class="service-create-section-icon service-icon-blue">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12.5 9.5 17 19 7" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Review input &amp; Submit</h2>
                        <p>Periksa kembali seluruh data sebelum permintaan dikirim.</p>
                    </div>
                    <span class="service-create-step">05</span>
                </div>

                <div class="service-wizard-review-notice">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/>
                        <path d="M12 11v5m0-8h.01" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round"/>
                    </svg>
                    <p>Setelah tombol <strong>Kirim Permintaan</strong> ditekan, permintaan akan disimpan dengan status
                        Pending. Pastikan data sudah benar sebelum dikirim untuk ditinjau admin.</p>
                </div>

                <div id="wizardReview" class="service-wizard-review"></div>
            </section>

            {{-- Wizard Footer --}}
            <div class="service-wizard-footer">
                <a href="{{ route('reservations.index') }}" class="service-cancel-btn">
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
                        <span data-wizard-submit-label>Kirim Permintaan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        (() => {
            const form = document.getElementById('reservationWizardForm');
            if (!form) return;

            const panels = Array.from(form.querySelectorAll('[data-wizard-panel]'));
            const indicators = Array.from(document.querySelectorAll('[data-wizard-indicator]'));
            const prevButton = form.querySelector('[data-wizard-prev]');
            const nextButton = form.querySelector('[data-wizard-next]');
            const submitButton = form.querySelector('[data-wizard-submit]');
            const submitLabel = form.querySelector('[data-wizard-submit-label]');
            const reviewRoot = document.getElementById('wizardReview');
            const guestContainer = document.getElementById('reservation-guests');
            const stopContainer = document.getElementById('reservation-stops');
            const guestCounter = document.getElementById('guestCounter');
            const stopCounter = document.getElementById('stopCounter');
            const serviceDate = document.getElementById('service_date');
            const serviceEndDate = document.getElementById('service_end_date');
            const maxGuests = 100;
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

            const updateGuestCounter = () => {
                if (!guestContainer || !guestCounter) return;
                const total = guestContainer.querySelectorAll('.service-stop-row').length;
                guestCounter.textContent = `${total} dari ${maxGuests} tamu`;
                const addGuest = document.getElementById('addGuest');
                if (addGuest) addGuest.disabled = total >= maxGuests;
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
                updateGuestCounter();
            });

            stopContainer?.addEventListener('click', (event) => {
                const button = event.target.closest('.service-remove-stop');
                if (!button || stopContainer.querySelectorAll('.service-stop-row').length <= 1) return;
                button.closest('.service-stop-row')?.remove();
                renumber(stopContainer);
                updateStopCounter();
            });

            document.getElementById('addGuest')?.addEventListener('click', () => {
                if (!guestContainer || guestContainer.querySelectorAll('.service-stop-row').length >= maxGuests) return;
                const i = nextGuestIndex++;
                const row = document.createElement('div');
                row.className = 'service-stop-row';
                row.innerHTML = `
                    <div class="service-stop-number"></div>
                    <div class="service-stop-fields">
                        <div class="service-create-field">
                            <label>Nama tamu <span>*</span></label>
                            <input name="guests[${i}][name]" type="text" maxlength="150" placeholder="Nama tamu" required>
                        </div>
                        <div class="service-create-field">
                            <label>Jenis kelamin <span>*</span></label>
                            <select name="guests[${i}][gender]" required>
                                <option value="">Pilih</option>
                                <option value="Male">Male (Pria)</option>
                                <option value="Female">Female (Wanita)</option>
                            </select>
                        </div>
                        <div class="service-create-field">
                            <label>Kategori usia <span>*</span></label>
                            <select name="guests[${i}][age_category]" required>
                                <option value="">Pilih</option>
                                <option value="Adult">Adult (Dewasa)</option>
                                <option value="Children">Children (Anak-anak)</option>
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
                updateGuestCounter();
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
                            <label>Nama destinasi <span>*</span></label>
                            <input name="itinerary_stops[${i}][name]" type="text" maxlength="150" required placeholder="Contoh: Uluwatu Temple">
                        </div>
                        <div class="service-create-field">
                            <label>Waktu kunjungan <span>*</span></label>
                            <input name="itinerary_stops[${i}][visit_time]" type="time" required>
                        </div>
                        <div class="service-create-field">
                            <label>Short link Google Maps <span>*</span></label>
                            <input name="itinerary_stops[${i}][google_maps_url]" type="url" maxlength="500" required placeholder="https://maps.app.goo.gl/...">
                        </div>
                        <div class="service-create-field">
                            <label>Keterangan</label>
                            <input name="itinerary_stops[${i}][note]" type="text" maxlength="500" placeholder="Keterangan destinasi (opsional)">
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

            const formatDate = (value) => {
                if (!value) return '';
                const parts = value.split('-');
                if (parts.length !== 3) return value;
                const date = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
                if (Number.isNaN(date.getTime())) return value;
                return new Intl.DateTimeFormat('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                }).format(date);
            };

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
                        const note = row.querySelector('input[name*="[note]"]')?.value.trim() || '';
                        return { name, time, map, note };
                    })
                    .filter(Boolean);

                const guestList = guests.length
                    ? `<ul class="service-wizard-review-list">${guests.map((guest) => `<li>${escapeHtml(guest.name)}${guest.gender || guest.age ? ` — ${escapeHtml([guest.gender, guest.age].filter(Boolean).join(' / '))}` : ''}${guest.phone ? ` — ${escapeHtml(guest.phone)}` : ''}</li>`).join('')}</ul>`
                    : '<p class="service-wizard-review-empty">Belum ada tamu pada daftar.</p>';

                const stopList = stops.length
                    ? `<ul class="service-wizard-review-list">${stops.map((stop) => `<li><strong>${escapeHtml(stop.time || '--:--')}</strong> — ${escapeHtml(stop.name)}${stop.note ? ` <em>(${escapeHtml(stop.note)})</em>` : ''}${stop.map ? ' — Maps tersedia' : ''}</li>`).join('')}</ul>`
                    : '<p class="service-wizard-review-empty">Belum ada destinasi pada itinerary.</p>';

                const guestCount = guests.length
                    ? `${guests.length} orang`
                    : (fieldValue('guest_count') ? `${fieldValue('guest_count')} orang` : '—');

                const startTime = stops.length ? stops[0].time : '';
                const endTime = stops.length ? stops[stops.length - 1].time : '';

                reviewRoot.innerHTML = [
                    reviewSection('Detail Layanan', [
                        ['Pelanggan / Agent', fieldValue('customer_name')],
                        ['Nomor Order', fieldValue('order_number')],
                        ['Tipe Layanan', selectedText('service_type_id', 'Belum dipilih')],
                        ['Jumlah Tamu', guestCount],
                        ['Tanggal Mulai', formatDate(fieldValue('service_date'))],
                        ['Jam Mulai', startTime],
                        ['Tanggal Akhir', formatDate(fieldValue('service_end_date'))],
                        ['Jam Selesai', endTime],
                    ]),
                    reviewSection('Daftar Tamu', [
                        ['Jumlah Tamu', guests.length ? `${guests.length} orang` : 'Belum ada tamu'],
                    ], guestList),
                    reviewSection('Itinerary', [
                        ['Jumlah Destinasi', stops.length ? `${stops.length} destinasi` : 'Belum ada destinasi'],
                    ], stopList),
                    reviewSection('Note', [
                        ['Catatan / Kebutuhan Khusus', fieldValue('notes')],
                    ]),
                ].join('');
            };

            nextButton?.addEventListener('click', () => {
                if (!validatePanel(panels[currentStep])) return;
                setStep(currentStep + 1);
            });

            prevButton?.addEventListener('click', () => setStep(currentStep - 1));

            form.addEventListener('keydown', (event) => {
                if (event.key !== 'Enter' || currentStep >= panels.length - 1) return;
                if (!event.target.matches('input, select')) return;
                event.preventDefault();
                nextButton?.click();
            });

            form.addEventListener('submit', (event) => {
                if (form.dataset.submitting === '1') {
                    event.preventDefault();
                    return;
                }

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

                form.dataset.submitting = '1';
                if (submitButton) submitButton.disabled = true;
                if (submitLabel) submitLabel.textContent = 'Mengirim Permintaan...';
            });

            renumber(guestContainer);
            renumber(stopContainer);
            updateGuestCounter();
            updateStopCounter();
            syncEndDateMinimum();
            setStep(currentStep, false);
        })();
    </script>
@endsection
