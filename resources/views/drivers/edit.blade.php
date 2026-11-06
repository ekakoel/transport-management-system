@extends('layouts.app')

@section('title', 'Edit Driver')

@section('content')
    <header>
        <div class="main-header">
            <div>
                <div class="service-create-eyebrow">
                    <span></span>
                    TRANSPORT OPERATIONS
                </div>
                <h1>Edit Driver</h1>
                <p>Perbarui data pengemudi, informasi SIM, dan status operasional.</p>
            </div>
        </div>
    </header>

    <div class="service-create-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
                <form id="driver-edit-form" method="POST" action="{{ route('drivers.update', $driver) }}"
                    class="service-create-form">
                    @csrf
                    @method('PUT')

                    <section class="service-create-card">
                        <div class="service-create-section-header">
                            <div class="service-create-section-icon service-icon-blue">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.7" />
                                    <path d="M4 21v-2a8 8 0 0 1 16 0v2" stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" />
                                </svg>
                            </div>
                            <div>
                                <h2>{{ $driver->name }}</h2>
                                <p>Ubah informasi driver yang sudah terdaftar.</p>
                            </div>
                            <span class="service-create-step">01</span>
                        </div>

                        <div class="service-create-fields">
                            <div class="service-create-field">
                                <label for="name">Nama Driver <span>*</span></label>
                                <input id="name" name="name" type="text" value="{{ old('name', $driver->name) }}"
                                    placeholder="Masukkan nama lengkap" autocomplete="name" required>
                                @error('name')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="phone">Nomor Telepon</label>
                                <input id="phone" name="phone" type="tel" value="{{ old('phone', $driver->phone) }}"
                                    placeholder="Contoh: +62 812..." autocomplete="tel">
                                @error('phone')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="license_number">Nomor SIM</label>
                                <input id="license_number" name="license_number" type="text"
                                    value="{{ old('license_number', $driver->license_number) }}"
                                    placeholder="Masukkan nomor SIM">
                                @error('license_number')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="notes">Catatan</label>
                                <input id="notes" name="notes" type="text" value="{{ old('notes', $driver->notes) }}"
                                    placeholder="Catatan tambahan (opsional)">
                                @error('notes')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="service-itinerary-info" style="margin-top: 20px;">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6" />
                                <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <label
                                style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer; margin: 0;">
                                <input type="checkbox" name="is_active" value="1" style="width: auto; margin: 0;"
                                    @checked(old('is_active', $driver->is_active ? '1' : '0') == '1')>
                                <span>
                                    <strong>Driver Aktif</strong>
                                    <small style="display: block;">Driver dapat dipilih untuk penugasan operasional.</small>
                                </span>
                            </label>
                        </div>

                        <div class="service-create-actions">
                            <button type="submit" class="service-submit-btn">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m5 12 4 4L19 6" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('drivers.index') }}" class="service-cancel-btn">Batal</a>
                        </div>
                    </section>
                </form>
            </div>

            <aside class="tms-info-panel" aria-label="Informasi Driver">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Ringkasan Driver</h2>
                    <p>Informasi driver yang sedang diubah.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Nama driver</span>
                        <strong class="tms-info-value">{{ $driver->name }}</strong>
                        <span class="tms-info-description">Nama pengemudi saat ini.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Nomor telepon</span>
                        <strong class="tms-info-value">{{ $driver->phone ?: '—' }}</strong>
                        <span class="tms-info-description">Kontak pengemudi.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Nomor SIM</span>
                        <strong class="tms-info-value">{{ $driver->license_number ?: '—' }}</strong>
                        <span class="tms-info-description">Identitas SIM pengemudi.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Status</span>
                        <strong class="tms-info-value">{{ $driver->is_active ? 'Aktif' : 'Nonaktif' }}</strong>
                        <span class="tms-info-description">Status operasional saat ini.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Dipakai pada layanan</span>
                        <strong class="tms-info-value">{{ number_format($serviceCount) }}</strong>
                        <span class="tms-info-description">Jumlah layanan dengan driver ini.</span>
                    </div>
                </div>
                <div class="tms-info-panel-footer">
                    <a href="{{ route('drivers.index') }}">&#8592; Kembali ke daftar driver</a>
                </div>
            </aside>
        </div>
    </div>
@endsection
