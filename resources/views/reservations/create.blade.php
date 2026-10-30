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
                    <div class="service-create-field">
                      <label for="customer_name">Nama pelanggan / Nama Agent <span>*</span></label>
                      <input id="customer_name" name="customer_name" type="text" value="{{ old('customer_name') }}" placeholder="Nama pelanggan / Nama Agent" required>
                      @error('customer_name')
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
              </form>
            </div>
        </div>
      </div>
    </div>
    <div class="resv-form-wrap">
        
        <form method="POST" action="{{ route('reservations.store') }}" class="resv-form-card">@csrf
            <div class="resv-fields">
                {{-- <div class="resv-field"><label for="customer_name">Nama pelanggan / pemesan *</label><input id="customer_name"
                        name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" maxlength="150"
                        required></div> --}}
                {{-- <div class="resv-field"><label for="customer_phone">Telepon</label><input id="customer_phone"
                        name="customer_phone" type="tel" value="{{ old('customer_phone') }}" maxlength="40"></div> --}}
                {{-- <div class="resv-field"><label for="guest_count">Jumlah tamu *</label><input id="guest_count"
                        name="guest_count" type="number" min="1" max="100" value="{{ old('guest_count', 1) }}"
                        required></div> --}}
                {{-- <div class="resv-field"><label for="service_type_id">Tipe layanan *</label><select id="service_type_id"
                        name="service_type_id" required>
                        <option value="">Pilih tipe</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}" @selected(old('service_type_id') == $type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select></div> --}}

                {{-- <div class="resv-field"><label for="service_date">Tanggal mulai *</label><input id="service_date"
                        type="date" name="service_date" min="{{ now('Asia/Makassar')->toDateString() }}"
                        value="{{ old('service_date') }}" required></div>
                <div class="resv-field"><label for="service_end_date">Tanggal selesai *</label><input id="service_end_date"
                        type="date" name="service_end_date" min="{{ now('Asia/Makassar')->toDateString() }}"
                        value="{{ old('service_end_date') }}" required></div> --}}
                {{-- <div class="resv-field"><label for="start_time">Jam mulai</label><input id="start_time" type="time"
                        name="start_time" value="{{ old('start_time') }}"></div>
                <div class="resv-field"><label for="end_time">Jam selesai</label><input id="end_time" type="time"
                        name="end_time" value="{{ old('end_time') }}"></div> --}}
                <div class="resv-field full">
                    <label>Itinerary / daftar tujuan</label>
                    <p class="resv-muted">Setiap destinasi disimpan terpisah dengan waktu kunjungan, nama, dan short link
                        Google Maps.</p>
                    <div id="reservation-stops">
                        <div class="resv-stop-row"
                            style="border:1px solid var(--tms-border);border-radius:10px;padding:14px;margin-bottom:12px">
                            <div class="resv-fields">
                                <div class="resv-field"><label>Waktu kunjungan</label><input type="time"
                                        name="itinerary_stops[0][visit_time]" required></div>
                                <div class="resv-field"><label>Nama destinasi</label><input type="text"
                                        name="itinerary_stops[0][name]" maxlength="150" placeholder="Ubud Palace" required>
                                </div>
                                <div class="resv-field full"><label>Short link Google Maps</label><input type="url"
                                        name="itinerary_stops[0][google_maps_url]" maxlength="500"
                                        placeholder="https://maps.app.goo.gl/..." required></div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="resv-btn alt" id="addReservationStop">+ Tambah destinasi</button>
                </div>
                <div class="resv-field full"><label>Daftar tamu / wisatawan</label>
                    <p class="resv-muted">Opsional bila data belum tersedia. Pemohon Reservation tidak otomatis menjadi
                        tamu.</p>
                    <div id="reservation-guests">
                        <div class="resv-guest-row"
                            style="border:1px solid var(--tms-border);border-radius:10px;padding:12px;margin-bottom:10px">
                            <div class="resv-fields">
                                <div class="resv-field"><label>Nama</label><input name="guests[0][name]" maxlength="150">
                                </div>
                                <div class="resv-field"><label>Jenis kelamin</label><select name="guests[0][gender]">
                                        <option value="">Pilih</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select></div>
                                <div class="resv-field"><label>Kategori usia</label><select name="guests[0][age_category]">
                                        <option value="">Pilih</option>
                                        <option value="Adult">Adult</option>
                                        <option value="Children">Children</option>
                                    </select></div>
                                <div class="resv-field"><label>Kontak (opsional)</label><input name="guests[0][phone]"
                                        type="tel" maxlength="40"></div>
                            </div>
                        </div>
                    </div><button type="button" class="resv-btn alt" id="addReservationGuest">+ Tambah tamu</button>
                </div>
                <div class="resv-field full"><label for="notes">Catatan / kebutuhan khusus</label>
                    <textarea id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="resv-actions"><a class="resv-btn alt" href="{{ route('reservations.index') }}">Batal</a><button
                    class="resv-btn" type="submit">Kirim Permintaan</button></div>
        </form>
    </div>
    <script>
        (() => {
            const guestContainer = document.getElementById('reservation-guests');
            const addGuest = document.getElementById('addReservationGuest');
            let guestIndex = 1;
            addGuest?.addEventListener('click', () => {
                if (!guestContainer || guestContainer.querySelectorAll('.resv-guest-row').length >= 100) return;
                const i = guestIndex++;
                const row = document.createElement('div');
                row.className = 'resv-guest-row';
                row.style.cssText =
                    'border:1px solid var(--tms-border);border-radius:10px;padding:12px;margin-bottom:10px';
                row.innerHTML =
                    '<div class="resv-fields"><div class="resv-field"><label>Nama</label><input name="guests[' +
                    i +
                    '][name]" maxlength="150"></div><div class="resv-field"><label>Jenis kelamin</label><select name="guests[' +
                    i +
                    '][gender]"><option value="">Pilih</option><option value="Male">Male</option><option value="Female">Female</option></select></div><div class="resv-field"><label>Kategori usia</label><select name="guests[' +
                    i +
                    '][age_category]"><option value="">Pilih</option><option value="Adult">Adult</option><option value="Children">Children</option></select></div><div class="resv-field"><label>Kontak (opsional)</label><input name="guests[' +
                    i +
                    '][phone]" maxlength="40"></div></div><button type="button" class="resv-btn alt remove-guest">Hapus tamu</button>';
                row.querySelector('.remove-guest').addEventListener('click', () => row.remove());
                guestContainer.appendChild(row);
            });
            const container = document.getElementById('reservation-stops');
            const add = document.getElementById('addReservationStop');
            if (!container || !add) return;
            let index = 1;
            add.addEventListener('click', () => {
                if (container.querySelectorAll('.resv-stop-row').length >= 40) return;
                const row = document.createElement('div');
                row.className = 'resv-stop-row';
                row.style.cssText =
                    'border:1px solid var(--tms-border);border-radius:10px;padding:14px;margin-bottom:12px';
                row.innerHTML =
                    '<div class="resv-fields"><div class="resv-field"><label>Waktu kunjungan</label><input type="time" name="itinerary_stops[' +
                    index +
                    '][visit_time]" required></div><div class="resv-field"><label>Nama destinasi</label><input type="text" name="itinerary_stops[' +
                    index +
                    '][name]" maxlength="150" required></div><div class="resv-field full"><label>Short link Google Maps</label><input type="url" name="itinerary_stops[' +
                    index +
                    '][google_maps_url]" maxlength="500" placeholder="https://maps.app.goo.gl/..." required></div></div><button type="button" class="resv-btn alt remove-stop">Hapus destinasi</button>';
                row.querySelector('.remove-stop').addEventListener('click', () => row.remove());
                container.appendChild(row);
                index++;
            });
        })();
    </script>
@endsection
