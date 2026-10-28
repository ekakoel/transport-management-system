@extends('layouts.app')

@section('title', 'Buat SPK')

@section('content')
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

        <form method="POST" action="{{ route('services.store') }}" class="service-create-form">
            @csrf

            {{-- Section 1: Customer and Service --}}
            <section class="service-create-card">
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
                        <label for="reservation_user_id">Pemilik Reservation / Kasus <span>*</span></label>
                        <select id="reservation_user_id" name="reservation_user_id" required>
                            <option value="">Pilih user ber-role reservation</option>
                            @foreach ($reservationUsers as $reservationUser)
                                <option value="{{ $reservationUser->id }}" @selected(old('reservation_user_id') == $reservationUser->id)>{{ $reservationUser->name }} ({{ $reservationUser->email }})</option>
                            @endforeach
                        </select>
                        @error('reservation_user_id')<small class="service-create-error">{{ $message }}</small>@enderror
                        <small>Pemilik kasus adalah staf internal Reservation, bukan wisatawan.</small>
                    </div>
                    <div class="service-create-field">
                        <label for="customer_name">Nama pelanggan (opsional)</label>
                        <input id="customer_name" name="customer_name" type="text" value="{{ old('customer_name') }}" placeholder="Kontak pelanggan jika diperlukan">
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

                    <div class="service-create-field">
                        <label for="pickup_location">Lokasi Penjemputan <span>*</span></label>
                        <input id="pickup_location" name="pickup_location" type="text"
                            value="{{ old('pickup_location') }}" placeholder="Hotel, bandara, atau alamat"
                            required>
                        @error('pickup_location')
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

            <section class="service-create-card">
                <div class="service-create-section-header"><div><h2>Daftar Tamu / Wisatawan</h2><p>Pemilik Reservation tidak otomatis menjadi tamu.</p></div><span class="service-create-step">02A</span></div>
                <div id="service-guests">
                    <div class="service-stop-row service-guest-row"><div class="service-stop-fields">
                        <div class="service-create-field"><label>Nama tamu</label><input name="guests[0][name]" maxlength="150"></div>
                        <div class="service-create-field"><label>Jenis kelamin</label><select name="guests[0][gender]"><option value="">Pilih</option><option value="Male">Male</option><option value="Female">Female</option></select></div>
                        <div class="service-create-field"><label>Kategori usia</label><select name="guests[0][age_category]"><option value="">Pilih</option><option value="Adult">Adult</option><option value="Children">Children</option></select></div>
                        <div class="service-create-field"><label>Kontak (opsional)</label><input name="guests[0][phone]" maxlength="40" type="tel"></div>
                    </div></div>
                </div>
                <button class="service-add-stop-btn" type="button" id="addGuest">+ Tambah tamu</button>
            </section>

            {{-- Section 2: Schedule --}}
            <section class="service-create-card">
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
                        <p>Tentukan periode dan waktu pelaksanaan layanan.</p>
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

                    <div class="service-create-field">
                        <label for="start_time">Jam Mulai</label>
                        <input id="start_time" type="time" name="start_time"
                            value="{{ old('start_time') }}">
                        @error('start_time')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="service-create-field">
                        <label for="end_time">Jam Selesai</label>
                        <input id="end_time" type="time" name="end_time"
                            value="{{ old('end_time') }}">
                        @error('end_time')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Section 3: Driver and Vehicle --}}
            <section class="service-create-card">
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
                    <span class="service-create-step">03</span>
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

            {{-- Section 4: Itinerary --}}
            <section class="service-create-card">
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
                    <span class="service-create-step">04</span>
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
                    <div class="service-stop-row">
                        <div class="service-stop-number">01</div>
                        <div class="service-stop-fields">
                            <div class="service-create-field">
                                <label for="stop-name-0">Nama Destinasi</label>
                                <input id="stop-name-0" name="stops[0][name]" type="text"
                                    value="{{ old('stops.0.name') }}"
                                    placeholder="Contoh: Uluwatu Temple">
                            </div>
                            <div class="service-create-field">
                                <label for="stop-time-0">Waktu Kunjungan <span>*</span></label>
                                <input id="stop-time-0" name="stops[0][visit_time]" type="time"
                                    value="{{ old('stops.0.visit_time') }}">
                            </div>
                            <div class="service-create-field">
                                <label for="stop-map-0">Short Link Google Maps <span>*</span></label>
                                <input id="stop-map-0" name="stops[0][google_maps_url]" type="url"
                                    value="{{ old('stops.0.google_maps_url') }}"
                                    placeholder="https://maps.app.goo.gl/...">
                            </div>
                            <div class="service-create-field">
                                <label for="stop-address-0">Alamat / Keterangan Lokasi</label>
                                <input id="stop-address-0" name="stops[0][address]" type="text"
                                    value="{{ old('stops.0.address') }}"
                                    placeholder="Alamat atau area destinasi">
                            </div>
                        </div>
                    </div>
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

            {{-- Form Actions --}}
            <div class="service-create-actions">
                <a href="{{ route('services.index') }}" class="service-cancel-btn">
                    Batal
                </a>
                <button type="submit" class="service-submit-btn">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12.5 9.5 17 19 7" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Terbitkan SPK
                </button>
            </div>
        </form>
    </div>

    <script>
        (() => {
            const guestContainer = document.getElementById('service-guests');
            const addGuestButton = document.getElementById('addGuest');
            let nextGuestIndex = 1;
            addGuestButton?.addEventListener('click', () => {
                if (!guestContainer || guestContainer.querySelectorAll('.service-guest-row').length >= 100) return;
                const i = nextGuestIndex++;
                const row = document.createElement('div');
                row.className = 'service-stop-row service-guest-row';
                row.innerHTML = '<div class="service-stop-fields"><div class="service-create-field"><label>Nama tamu</label><input name="guests['+i+'][name]" maxlength="150"></div><div class="service-create-field"><label>Jenis kelamin</label><select name="guests['+i+'][gender]"><option value="">Pilih</option><option value="Male">Male</option><option value="Female">Female</option></select></div><div class="service-create-field"><label>Kategori usia</label><select name="guests['+i+'][age_category]"><option value="">Pilih</option><option value="Adult">Adult</option><option value="Children">Children</option></select></div><div class="service-create-field"><label>Kontak (opsional)</label><input name="guests['+i+'][phone]" maxlength="40" type="tel"></div></div><button type="button" class="service-remove-stop">Hapus tamu</button>';
                row.querySelector('.service-remove-stop').addEventListener('click', () => row.remove());
                guestContainer.appendChild(row);
            });
            const stopsContainer = document.getElementById('stops');
            const addButton = document.getElementById('addStop');
            const counter = document.getElementById('stopCounter');
            const serviceDate = document.getElementById('service_date');
            const serviceEndDate = document.getElementById('service_end_date');

            if (!stopsContainer || !addButton || !counter) return;

            let nextStopIndex = 1;
            let stopCount = 1;
            const maxStops = 40;

            function updateStopCounter() {
                counter.textContent = `${stopCount} dari ${maxStops} destinasi`;
                addButton.disabled = stopCount >= maxStops;
                addButton.innerHTML = stopCount >= maxStops
                    ? 'Maksimal 40 destinasi tercapai'
                    : `<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round"/>
                       </svg> Tambah Destinasi`;
            }

            addButton.addEventListener('click', () => {
                if (stopCount >= maxStops) return;

                const index = nextStopIndex++;
                const number = String(stopCount + 1).padStart(2, '0');
                const row = document.createElement('div');

                row.className = 'service-stop-row';
                row.innerHTML = `
                    <div class="service-stop-number">${number}</div>
                    <div class="service-stop-fields">
                        <div class="service-create-field">
                            <label for="stop-name-${index}">Nama Destinasi</label>
                            <input id="stop-name-${index}" name="stops[${index}][name]"
                                type="text" placeholder="Nama destinasi">
                        </div>
                        <div class="service-create-field">
                            <label for="stop-time-${index}">Waktu Kunjungan *</label>
                            <input id="stop-time-${index}" name="stops[${index}][visit_time]"
                                type="time">
                        </div>
                        <div class="service-create-field">
                            <label for="stop-map-${index}">Short Link Google Maps *</label>
                            <input id="stop-map-${index}" name="stops[${index}][google_maps_url]"
                                type="url" placeholder="https://maps.app.goo.gl/...">
                        </div>
                        <div class="service-create-field">
                            <label for="stop-address-${index}">Alamat / Keterangan Lokasi</label>
                            <input id="stop-address-${index}" name="stops[${index}][address]"
                                type="text" placeholder="Alamat atau area destinasi">
                        </div>
                    </div>
                    <button type="button" class="service-remove-stop"
                        aria-label="Hapus destinasi ${number}" title="Hapus destinasi">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3"
                                stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                `;

                row.querySelector('.service-remove-stop').addEventListener('click', () => {
                    row.remove();
                    stopCount--;
                    renumberStops();
                    updateStopCounter();
                });

                stopsContainer.appendChild(row);
                stopCount++;
                updateStopCounter();
            });

            function renumberStops() {
                stopsContainer.querySelectorAll('.service-stop-row').forEach((row, index) => {
                    row.querySelector('.service-stop-number').textContent =
                        String(index + 1).padStart(2, '0');
                });
            }

            function syncEndDateMinimum() {
                if (!serviceDate || !serviceEndDate || !serviceDate.value) return;

                serviceEndDate.min = serviceDate.value;

                if (serviceEndDate.value && serviceEndDate.value < serviceDate.value) {
                    serviceEndDate.value = serviceDate.value;
                }
            }

            serviceDate?.addEventListener('change', syncEndDateMinimum);
            syncEndDateMinimum();
            updateStopCounter();
        })();
    </script>
@endsection