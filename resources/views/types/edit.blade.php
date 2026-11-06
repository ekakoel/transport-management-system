@extends('layouts.app')

@section('title', 'Edit Tipe Layanan')

@section('content')
    <header>
        <div class="main-header">
            <div>
                <div class="service-create-eyebrow">
                    <span></span>
                    TRANSPORT OPERATIONS
                </div>
                <h1>Edit Tipe Layanan</h1>
                <p>Perbarui nama, deskripsi, atau status tipe layanan.</p>
            </div>
        </div>
    </header>

    <div class="service-create-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
                <form id="type-edit-form" method="POST" action="{{ route('types.update', $type) }}"
                    class="service-create-form">
                    @csrf
                    @method('PUT')

                    <section class="service-create-card">
                        <div class="service-create-section-header">
                            <div class="service-create-section-icon service-icon-blue">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor"
                                        stroke-width="1.7" />
                                    <path d="M8 9h8M8 12h8M8 15h4" stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" />
                                </svg>
                            </div>
                            <div>
                                <h2>{{ $type->name }}</h2>
                                <p>Ubah informasi tipe layanan yang sudah terdaftar.</p>
                            </div>
                            <span class="service-create-step">01</span>
                        </div>

                        <div class="service-create-fields">
                            <div class="service-create-field">
                                <label for="name">Tipe Layanan <span>*</span></label>
                                <input id="name" type="text" name="name" value="{{ old('name', $type->name) }}"
                                    placeholder="Contoh: Airport Transfer" required>
                                @error('name')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="description">Deskripsi</label>
                                <input id="description" type="text" name="description"
                                    value="{{ old('description', $type->description) }}"
                                    placeholder="Deskripsi singkat layanan">
                                @error('description')
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
                                    @checked(old('is_active', $type->is_active ? '1' : '0') == '1')>
                                <span>
                                    <strong>Tipe Layanan Aktif</strong>
                                    <small style="display: block;">Tipe layanan ditandai aktif untuk penggunaan
                                        operasional.</small>
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
                            <a href="{{ route('types.index') }}" class="service-cancel-btn">Batal</a>
                        </div>
                    </section>
                </form>
            </div>

            <aside class="tms-info-panel" aria-label="Informasi Tipe Layanan">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Ringkasan Tipe</h2>
                    <p>Informasi tipe layanan yang sedang diubah.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Nama tipe</span>
                        <strong class="tms-info-value">{{ $type->name }}</strong>
                        <span class="tms-info-description">Nama tipe layanan saat ini.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Status</span>
                        <strong class="tms-info-value">{{ $type->is_active ? 'Aktif' : 'Nonaktif' }}</strong>
                        <span class="tms-info-description">Status penggunaan tipe layanan.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Dipakai pada layanan</span>
                        <strong class="tms-info-value">{{ number_format($serviceCount) }}</strong>
                        <span class="tms-info-description">Jumlah layanan transportasi dengan tipe ini.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Dipakai pada reservasi</span>
                        <strong class="tms-info-value">{{ number_format($reservationCount) }}</strong>
                        <span class="tms-info-description">Jumlah permintaan layanan dengan tipe ini.</span>
                    </div>
                </div>
                <div class="tms-info-panel-footer">
                    <a href="{{ route('types.index') }}">&#8592; Kembali ke daftar tipe layanan</a>
                </div>
            </aside>
        </div>
    </div>
@endsection
