@extends('layouts.app')
@section('title', 'Buat Permintaan Transportasi')
@section('content')
  
    <header>
        <div class="main-header">
            <div class="services-heading">
                <div class="services-eyebrow">
                    <span class="services-eyebrow-dot"></span>
                    LAYANAN TRANSPORTASI
                </div>
                <h1>Buat Permintaan Transportasi</h1>
                <p>Permintaan akan berstatus Pending sampai ditinjau admin. SPK baru dibuat setelah disetujui.</p>
            </div>
        </div>
    </header>
    {{-- Information Banner --}}
    <div class="service-create-page">
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
              <p>Pastikan data layanan, jadwal, dan destinasi sudah benar. Permintaab layanan dikirimkan setelah validasi permintaan layanan berhasil.</p>
          </div>
      </div>
      <div class="services-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
              <form method="POST" action="{{ route('reservations.store') }}" class="service-create-form">
                @csrf
                {{-- Section 1: Customer and Service --}}
                <section class="service-create-card">
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
                      <input id="customer_name" name="customer_name" type="text" value="{{ old('customer_name', auth()->user()->name) }}" placeholder="Nama pelanggan / Nama Agent" required>
                      @error('customer_name')
                          <small class="service-create-error">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="service-create-field">
                      <label for="customer_phone">Nomor WhatsApp / Telepon <span>*</span></label>
                      <input id="customer_phone" name="customer_phone" type="tel" maxlength="40" value="{{ old('customer_phone', auth()->user()->phone) }}" placeholder="Contoh: +62 812..." required>
                      @error('customer_phone')
                          <small class="service-create-error">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="service-create-field">
                      <label for="guest_count">Jumlah Tamu</label>
                      <input id="guest_count" name="guest_count" type="number" min="1" max="100" value="{{ old('guest_count') }}" required>
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
                      <label for="start_time">Jam Mulai</label>
                      <input id="start_time" type="time" name="start_time"
                          value="{{ old('start_time') }}">
                      @error('start_time')
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
                      <label for="end_time">Jam Selesai</label>
                      <input id="end_time" type="time" name="end_time"
                          value="{{ old('end_time') }}">
                      @error('end_time')
                          <small class="service-create-error">{{ $message }}</small>
                      @enderror
                    </div>
                  </div>
                </section>
                {{-- Section 2: Guests List --}}
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
                            <h2>Daftar Tamu</h2>
                            <p>Tambahkan daftar tamu sesuai dengan jumlah pax</p>
                        </div>
                        <span class="service-create-step">02</span>
                    </div>
                    <div id="reservation-guests" class="service-stops">
                        <div class="service-stop-row">
                            <div class="service-stop-number">1</div>
                            <div class="service-stop-fields">
                                <div class="service-create-field">
                                    <label for="guests-name-0">Nama Tamu <span>*</span></label>
                                    <input id="guests-name-0" name="guests[0][name]" type="text"
                                        value="{{ old('guests[0][name]') }}" required
                                        placeholder="Masukan nama tamu">
                                    @error('guests[0][name]')
                                        <small class="service-create-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="service-create-field">
                                    <div class="service-create-field">
                                        <label for="guests-gender-0">Jenis Kelamin <span>*</span></label>
                                        <select id="guests-gender-0" name="guests[0][gender]" required>
                                            <option value="" selected>Pilih jenis kelamin</option>
                                            <option value="Male" @selected(old('guests[0][gender]') == "Male")>Male (Pria)</option>
                                            <option value="Female" @selected(old('guests[0][gender]') == "Female")>Female (Wanita)</option>
                                        </select>
                                        @error('guests[0][gender]')
                                            <small class="service-create-error">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="service-create-field">
                                    <div class="service-create-field">
                                        <label for="guests-age-category-0">Kategori Usia<span>*</span></label>
                                        <select id="guests-age-category-0" name="guests[0][age_category]" required>
                                            <option value="" selected>Pilih kategori usia</option>
                                            <option value="Children" @selected(old('guests[0][age_category]') == "Children")>Children (Anak-anak)</option>
                                            <option value="Adult" @selected(old('guests[0][age_category]') == "Adult")>Adult (Dewasa)</option>
                                        </select>
                                        @error('guests[0][age_category]')
                                            <small class="service-create-error">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="service-create-field">
                                    <label for="guests-kontak-0">Kontak (opsional)</label>
                                    <input id="guests-kontak-0" name="guests[0][phone]" type="text"
                                        value="{{ old('guests[0][phone]') }}"
                                        placeholder="Nomor telepon tamu">
                                    @error('guests[0][phone]')
                                        <small class="service-create-error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="service-add-stop-btn" type="button" id="addReservationGuest">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        Tambah Tamu
                    </button>
                </section>
                {{-- Section 3: Itinerary --}}
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

                    <div id="reservation-stops" class="service-stops">
                        <div class="service-stop-row">
                            <div class="service-stop-number">1</div>
                            <div class="service-stop-fields">
                                <div class="service-create-field">
                                    <label for="stop-name-0">Nama Destinasi</label>
                                    <input id="stop-name-0" name="stops[0][name]" type="text"
                                        value="{{ old('stops.0.name') }}" required
                                        placeholder="Contoh: Uluwatu Temple">
                                </div>
                                <div class="service-create-field">
                                    <label for="stop-time-0">Waktu Kunjungan <span>*</span></label>
                                    <input id="stop-time-0" name="stops[0][visit_time]" type="time"
                                        value="{{ old('stops.0.visit_time') }}" required>
                                </div>
                                <div class="service-create-field">
                                    <label for="stop-map-0">Short Link Google Maps <span>*</span></label>
                                    <input id="stop-map-0" name="stops[0][google_maps_url]" type="url"
                                        value="{{ old('stops.0.google_maps_url') }}" required
                                        placeholder="https://maps.app.goo.gl/...">
                                </div>
                                <div class="service-create-field">
                                    <label for="stop-note-0">Keterangan</label>
                                    <input id="stop-note-0" name="stops[0][note]" type="text"
                                        value="{{ old('stops.0.note') }}"
                                        placeholder="Tambahkan keternangan untuk destinasi ini">
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="service-add-stop-btn" type="button" id="addReservationStop">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        Tambah Destinasi
                    </button>
                    <p class="service-stop-counter" id="stopCounter">1 dari 40 destinasi</p>
                </section>
                {{-- Section 4: Note --}}
                <section class="service-create-card">
                    <div class="service-create-section-header">
                        <div class="service-create-section-icon service-show-blue">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M7 3.5h7l5 5V14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14 3.5v5h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7 3.5H5a1.5 1.5 0 0 0-1.5 1.5v14A1.5 1.5 0 0 0 5 20.5h8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7 12h7M7 15.5h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <circle cx="17.5" cy="17.5" r="3.5" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M17.5 15.8v3.4M15.8 17.5h3.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <h2>Note</h2>
                            <p>Tambahkan note untuk admin</p>
                        </div>
                        <span class="service-create-step">04</span>
                    </div>
                    <div class="service-create-field">
                        <label for="notes"></label>
                            <label for="notes">Catatan / kebutuhan khusus</label>
                            <textarea id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    </div>
                </section>
                <div class="service-create-card text-right">
                    <a class="service-cancel-btn" href="{{ route('reservations.index') }}">Batal</a>
                    <button class="service-wizard-btn service-wizard-btn-primary" type="submit">Kirim Permintaan</button>
                </div>
              </form>
            </div>
        </div>
      </div>
    </div>
    <div class="resv-form-wrap">
        
        {{-- <form method="POST" action="{{ route('reservations.store') }}" class="resv-form-card">@csrf
            <div class="resv-fields">
                <label for=""></label>
                <div class="resv-field full"><label for="notes">Catatan / kebutuhan khusus</label>
                    <textarea id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="resv-actions">
                <a class="resv-btn alt" href="{{ route('reservations.index') }}">Batal</a>
                <button class="resv-btn" type="submit">Kirim Permintaan</button></div>
        </form> --}}
    </div>
    <script>
        (() => {
            const guestContainer = document.getElementById('reservation-guests');
            const addGuest = document.getElementById('addReservationGuest');
            let guestIndex = 1;
            addGuest?.addEventListener('click', () => {
                if (!guestContainer || guestContainer.querySelectorAll('.resv-guest-row').length >= 100) return;
                const i = guestIndex++;
                const ino = i + 1;
                const row = document.createElement('div');
                row.className = 'service-stop-row';
                row.innerHTML =
                    '<div class="service-stop-number">'+ ino +'</div>'+
                    '<div class="service-create-fields"><div class="service-create-field"><label>Nama</label><input id="guest-name-' + i + '" name="guests[' +
                    i +
                    '][name]" maxlength="150"></div><div class="service-create-field"><label>Jenis kelamin</label><select name="guests[' +
                    i +
                    '][gender]"><option value="">Pilih</option><option value="Male">Male</option><option value="Female">Female</option></select></div><div class="service-create-field"><label>Kategori usia</label><select name="guests[' +
                    i +
                    '][age_category]"><option value="">Pilih</option><option value="Adult">Adult</option><option value="Children">Children</option></select></div><div class="service-create-field"><label>Kontak (opsional)</label><input name="guests[' +
                    i +
                    '][phone]" maxlength="40"></div></div><button type="button" class="resv-btn-remove alt remove-guest">X</button>';
                row.querySelector('.remove-guest').addEventListener('click', () => row.remove());
                guestContainer.appendChild(row);
            });
            const container = document.getElementById('reservation-stops');
            const add = document.getElementById('addReservationStop');
            if (!container || !add) return;
            let index = 1;
            add.addEventListener('click', () => {
                if (container.querySelectorAll('.service-stop-row').length >= 40) return;
                const row = document.createElement('div');
                const noIndex = index + 1;
                row.className = 'service-stop-row';
                row.innerHTML =
                    '<div class="service-stop-number">'+ noIndex +'</div>'+
                    '<div class="service-stop-fields"><div class="service-create-field"><div class="service-create-field"><label>Nama destinasi</label><input type="text" placeholder="Contoh: Uluwatu Temple" id="stop-name-' +
                    index +'" name="itinerary_stops[' +
                    index +
                    '][name]" required></div></div><div class="service-create-field"><label>Waktu Kunjungan</label><input type="time" id="stop-time-' + 
                    index +'" name="itinerary_stops[' +
                    index +
                    '][visit_time]" maxlength="150" required></div> <div class="service-create-field full"><label>Short link Google Maps</label><input type="url" id="stop-map-' +
                    index +'" name="itinerary_stops[' +
                    index +
                    '][google_maps_url]" maxlength="500" placeholder="https://maps.app.goo.gl/..." required></div><div class="service-create-field full"><label>Keterangan</label><input type="text" id="stop-note-' + index + '" name="stops[' + 
                    index + '][note]"></div></div> <button type="button" class="resv-btn-remove alt remove-stop">X</button>';
                row.querySelector('.remove-stop').addEventListener('click', () => row.remove());
                container.appendChild(row);
                index++;
            });
        })();
    </script>
@endsection
