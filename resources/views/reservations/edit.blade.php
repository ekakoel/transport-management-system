@extends('layouts.app')
@section('title', 'Edit Permintaan Transportasi')
@section('content')
    @php
        $guestRows = old('guests', $reservation->guests->map(fn ($guest) => [
            'name' => $guest->name,
            'gender' => $guest->gender,
            'age_category' => $guest->age_category,
            'phone' => $guest->phone,
        ])->all());
        if (!is_array($guestRows) || count($guestRows) === 0) {
            $guestRows = [['name' => '', 'gender' => '', 'age_category' => '', 'phone' => '']];
        }

        $stopRows = old('itinerary_stops', $reservation->itinerary ?? []);
        if (!is_array($stopRows) || count($stopRows) === 0) {
            $stopRows = [['name' => '', 'visit_time' => '', 'google_maps_url' => '']];
        }
    @endphp
    <header>
        <div class="main-header">
            <div class="services-heading">
                <div class="services-eyebrow">
                    <span class="services-eyebrow-dot"></span>
                    PERMINTAAN LAYANAN TRANSPORTASI
                </div>
                <h1>Edit {{ $reservation->reservation_number }}</h1>
                <p>Perbarui data permintaan layanan. Penyesuaian hanya dapat dilakukan sebelum tanggal layanan tiba.</p>
            </div>
            <a href="{{ route('admin.reservations.show', $reservation) }}" class="service-show-back">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M19 12H5m7 7-7-7 7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                Kembali ke detail permintaan
            </a>
        </div>
    </header>
    <div class="service-create-page">
        <form method="POST" action="{{ route('admin.reservations.update', $reservation) }}" class="service-create-form">
            @csrf
            @method('PUT')
            <section class="service-create-card">
                <div class="service-create-section-header">
                    <div class="service-create-section-icon service-icon-blue">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M16 20v-1.5a4.5 4.5 0 0 0-4.5-4.5h-3A4.5 4.5 0 0 0 4 18.5V20"
                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                            <circle cx="10" cy="7" r="4" stroke="currentColor" stroke-width="1.7" />
                            <path d="M16 4.5a4 4 0 0 1 0 7.5m2 3a4.5 4.5 0 0 1 2 3.5V20"
                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                        </svg>
                    </div>
                    <div>
                        <h2>Detail Layanan</h2>
                        <p>Informasi agen dan kebutuhan transportasi.</p>
                    </div>
                    <span class="service-create-step">01</span>
                </div>

                <div class="service-create-fields">
                    <div class="service-create-field">
                        <label for="customer_name">Nama agen / Kode Agen <span>*</span></label>
                        <input id="customer_name" name="customer_name" type="text" maxlength="150"
                            value="{{ old('customer_name', $reservation->customer_name) }}"
                            placeholder="Nama agen / Kode Agen" required>
                        @error('customer_name')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="service-create-field">
                        <label for="order_number">Nomor Order <span>*</span></label>
                        <input id="order_number" name="order_number" type="text" maxlength="100"
                            value="{{ old('order_number', $reservation->order_number) }}"
                            placeholder="Nomor order dari agen / sistem" required>
                        <small class="service-create-help">Opsional. Dapat diisi oleh admin saat membuat SPK.</small>
                        @error('order_number')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="service-create-field">
                        <label for="service_type_id">Tipe Layanan <span>*</span></label>
                        <select id="service_type_id" name="service_type_id" required>
                            <option value="">Pilih tipe layanan</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" @selected(old('service_type_id', $reservation->service_type_id) == $type->id)>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_type_id')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="service-create-field">
                        <label for="guest_count">Jumlah Tamu <span>*</span></label>
                        <input id="guest_count" name="guest_count" type="number" min="1" max="100"
                            value="{{ old('guest_count', $reservation->guest_count ?: 1) }}" required>
                        <small class="service-create-help">Otomatis mengikuti daftar tamu bila daftar tamu diisi.</small>
                        @error('guest_count')
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
                            value="{{ old('service_date', $reservation->service_date->format('Y-m-d')) }}" required>
                        @error('service_date')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="service-create-field">
                        <label for="service_end_date">Tanggal Akhir <span>*</span></label>
                        <input id="service_end_date" type="date" name="service_end_date"
                            min="{{ now('Asia/Makassar')->toDateString() }}"
                            value="{{ old('service_end_date', $reservation->service_end_date->format('Y-m-d')) }}" required>
                        @error('service_end_date')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="service-create-field service-field-full">
                        <label for="notes">Catatan / Kebutuhan Khusus</label>
                        <textarea id="notes" name="notes" rows="3"
                            placeholder="Permintaan khusus, informasi tamu, atau instruksi tambahan...">{{ old('notes', $reservation->notes) }}</textarea>
                        @error('notes')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </section>

            <section class="service-create-card">
                <div class="service-create-section-header">
                    <div class="service-create-section-icon service-icon-blue">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="9" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7" />
                            <path d="M2.5 20v-1.5a6.5 6.5 0 0 1 13 0V20" stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round" />
                            <path d="M16 5.2a3.5 3.5 0 0 1 0 6.6M18 14a5.5 5.5 0 0 1 3.5 5.1V20" stroke="currentColor"
                                stroke-width="1.7" stroke-linecap="round" />
                        </svg>
                    </div>
                    <div>
                        <h2>Daftar Tamu</h2>
                        <p>Daftar wisatawan yang mengikuti layanan. Boleh dikosongkan bila data belum tersedia.</p>
                    </div>
                    <span class="service-create-step">02</span>
                </div>

                <div id="guests" class="service-stops">
                    @foreach ($guestRows as $index => $guest)
                        <div class="service-stop-row">
                            <div class="service-stop-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="service-stop-fields">
                                <div class="service-create-field">
                                    <label>Nama tamu</label>
                                    <input name="guests[{{ $index }}][name]" type="text" maxlength="150"
                                        value="{{ $guest['name'] ?? '' }}" placeholder="Nama tamu">
                                </div>
                                <div class="service-create-field">
                                    <label>Jenis kelamin</label>
                                    <select name="guests[{{ $index }}][gender]">
                                        <option value="">Pilih</option>
                                        <option value="Male" @selected(($guest['gender'] ?? '') === 'Male')>Male</option>
                                        <option value="Female" @selected(($guest['gender'] ?? '') === 'Female')>Female</option>
                                    </select>
                                </div>
                                <div class="service-create-field">
                                    <label>Kategori usia</label>
                                    <select name="guests[{{ $index }}][age_category]">
                                        <option value="">Pilih</option>
                                        <option value="Adult" @selected(($guest['age_category'] ?? '') === 'Adult')>Adult</option>
                                        <option value="Children" @selected(($guest['age_category'] ?? '') === 'Children')>Children</option>
                                    </select>
                                </div>
                                <div class="service-create-field">
                                    <label>Kontak (opsional)</label>
                                    <input name="guests[{{ $index }}][phone]" type="tel" maxlength="40"
                                        value="{{ $guest['phone'] ?? '' }}" placeholder="Nomor telepon tamu">
                                </div>
                            </div>
                            <button type="button" class="service-remove-stop" aria-label="Hapus tamu">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 7h16M9 7V5h6v2m-8 0 1 13h8l1-13" stroke="currentColor"
                                        stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                <button class="service-add-stop-btn" type="button" id="addGuestRow">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                    </svg>
                    Tambah Tamu
                </button>
            </section>

            <section class="service-create-card">
                <div class="service-create-section-header">
                    <div class="service-create-section-icon service-icon-orange">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 21s7-6.1 7-12a7 7 0 1 0-14 0c0 5.9 7 12 7 12Z" stroke="currentColor"
                                stroke-width="1.7" />
                            <circle cx="12" cy="9" r="2.5" stroke="currentColor" stroke-width="1.7" />
                        </svg>
                    </div>
                    <div>
                        <h2>Itinerary Awal</h2>
                        <p>Susun destinasi yang direncanakan selama layanan berlangsung.</p>
                    </div>
                    <span class="service-create-step">03</span>
                </div>

                <div id="stops" class="service-stops">
                    @foreach ($stopRows as $index => $stop)
                        <div class="service-stop-row">
                            <div class="service-stop-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="service-stop-fields">
                                <div class="service-create-field">
                                    <label>Nama destinasi</label>
                                    <input name="itinerary_stops[{{ $index }}][name]" type="text" maxlength="150"
                                        value="{{ $stop['name'] ?? '' }}" placeholder="Contoh: Uluwatu Temple" required>
                                </div>
                                <div class="service-create-field">
                                    <label>Waktu kunjungan</label>
                                    <input name="itinerary_stops[{{ $index }}][visit_time]" type="time"
                                        value="{{ substr((string) ($stop['visit_time'] ?? ''), 0, 5) }}" required>
                                </div>
                                <div class="service-create-field service-field-full">
                                    <label>Short link Google Maps</label>
                                    <input name="itinerary_stops[{{ $index }}][google_maps_url]" type="url"
                                        maxlength="500" value="{{ $stop['google_maps_url'] ?? '' }}"
                                        placeholder="https://maps.app.goo.gl/..." required>
                                </div>
                            </div>
                            <button type="button" class="service-remove-stop" aria-label="Hapus destinasi">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 7h16M9 7V5h6v2m-8 0 1 13h8l1-13" stroke="currentColor"
                                        stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                <button class="service-add-stop-btn" type="button" id="addStopRow">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                    </svg>
                    Tambah Destinasi
                </button>
                <p class="service-stop-counter" id="stopCounter">{{ count($stopRows) }} dari 40 destinasi</p>
            </section>

            <div class="service-create-actions">
                <a href="{{ route('admin.reservations.show', $reservation) }}" class="service-show-back">Batal</a>
                <button class="service-submit-btn" type="submit">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <script>
        (() => {
            const guestContainer = document.getElementById('guests');
            const addGuest = document.getElementById('addGuestRow');
            const stopContainer = document.getElementById('stops');
            const addStop = document.getElementById('addStopRow');
            const stopCounter = document.getElementById('stopCounter');
            let guestIndex = {{ count($guestRows) }};
            let stopIndex = {{ count($stopRows) }};

            const renumber = (container) => {
                container?.querySelectorAll('.service-stop-row').forEach((row, position) => {
                    const number = row.querySelector('.service-stop-number');
                    if (number) number.textContent = String(position + 1).padStart(2, '0');
                });
            };

            const updateStopCounter = () => {
                if (!stopContainer || !stopCounter) return;
                const total = stopContainer.querySelectorAll('.service-stop-row').length;
                stopCounter.textContent = `${total} dari 40 destinasi`;
                if (addStop) addStop.disabled = total >= 40;
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
                if (!button) return;
                button.closest('.service-stop-row')?.remove();
                renumber(stopContainer);
                updateStopCounter();
            });

            addGuest?.addEventListener('click', () => {
                if (!guestContainer || guestContainer.querySelectorAll('.service-stop-row').length >= 100) return;
                const i = guestIndex++;
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

            addStop?.addEventListener('click', () => {
                if (!stopContainer || stopContainer.querySelectorAll('.service-stop-row').length >= 40) return;
                const i = stopIndex++;
                const row = document.createElement('div');
                row.className = 'service-stop-row';
                row.innerHTML = `
                    <div class="service-stop-number"></div>
                    <div class="service-stop-fields">
                        <div class="service-create-field">
                            <label>Nama destinasi</label>
                            <input name="itinerary_stops[${i}][name]" type="text" maxlength="150" placeholder="Contoh: Uluwatu Temple" required>
                        </div>
                        <div class="service-create-field">
                            <label>Waktu kunjungan</label>
                            <input name="itinerary_stops[${i}][visit_time]" type="time" required>
                        </div>
                        <div class="service-create-field service-field-full">
                            <label>Short link Google Maps</label>
                            <input name="itinerary_stops[${i}][google_maps_url]" type="url" maxlength="500" placeholder="https://maps.app.goo.gl/..." required>
                        </div>
                    </div>
                    <button type="button" class="service-remove-stop" aria-label="Hapus destinasi">${removeIcon}</button>
                `;
                stopContainer.appendChild(row);
                renumber(stopContainer);
                updateStopCounter();
            });

            updateStopCounter();
        })();
    </script>
@endsection
