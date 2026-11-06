@extends('layouts.app')

@section('title', 'Edit Kendaraan')

@section('content')
    <header>
        <div class="main-header">
            <div>
                <div class="service-create-eyebrow">
                    <span></span>
                    TRANSPORT OPERATIONS
                </div>
                <h1>Edit Kendaraan</h1>
                <p>Perbarui data armada, kapasitas, dan status operasional kendaraan.</p>
            </div>
        </div>
    </header>

    <div class="service-create-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
                <form id="vehicle-edit-form" method="POST" action="{{ route('vehicles.update', $vehicle) }}"
                    class="service-create-form">
                    @csrf
                    @method('PUT')

                    <section class="service-create-card">
                        <div class="service-create-section-header">
                            <div class="service-create-section-icon service-icon-blue">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m5 11 2-5h10l2 5M4 11h16v7H4z" stroke="currentColor" stroke-width="1.7"
                                        stroke-linejoin="round" />
                                    <path d="M7 18v2m10-2v2M4 14h3m10 0h3" stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" />
                                    <circle cx="7.5" cy="14.5" r=".8" fill="currentColor" />
                                    <circle cx="16.5" cy="14.5" r=".8" fill="currentColor" />
                                </svg>
                            </div>
                            <div>
                                <h2>{{ $vehicle->name }}</h2>
                                <p>Ubah informasi kendaraan yang sudah terdaftar.</p>
                            </div>
                            <span class="service-create-step">01</span>
                        </div>

                        <div class="service-create-fields">
                            <div class="service-create-field">
                                <label for="name">Nama / Model Kendaraan <span>*</span></label>
                                <input id="name" name="name" type="text"
                                    value="{{ old('name', $vehicle->name) }}" placeholder="Contoh: Toyota Innova Zenix"
                                    required>
                                @error('name')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="plate_number">Nomor Polisi <span>*</span></label>
                                <input id="plate_number" name="plate_number" type="text"
                                    value="{{ old('plate_number', $vehicle->plate_number) }}"
                                    placeholder="Contoh: DK 1234 ABC" required>
                                @error('plate_number')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="capacity">Kapasitas Penumpang <span>*</span></label>
                                <input id="capacity" name="capacity" type="number" min="1" max="100"
                                    value="{{ old('capacity', $vehicle->capacity) }}" placeholder="Jumlah penumpang"
                                    required>
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
                                    @foreach (['Available', 'Assigned', 'Maintenance', 'Inactive'] as $status)
                                        <option value="{{ $status }}" @selected(old('status', $vehicle->status) === $status)>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field service-field-full">
                                <label for="notes">Catatan Kendaraan</label>
                                <textarea id="notes" name="notes" rows="3"
                                    placeholder="Informasi tambahan, kondisi kendaraan, atau catatan operasional...">{{ old('notes', $vehicle->notes) }}</textarea>
                                @error('notes')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="service-create-actions">
                            <button type="submit" class="service-submit-btn">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m5 12 4 4L19 6" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('vehicles.index') }}" class="service-cancel-btn">Batal</a>
                        </div>
                    </section>
                </form>
            </div>

            <aside class="tms-info-panel" aria-label="Informasi Kendaraan">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Ringkasan Kendaraan</h2>
                    <p>Informasi kendaraan yang sedang diubah.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Nama kendaraan</span>
                        <strong class="tms-info-value">{{ $vehicle->name }}</strong>
                        <span class="tms-info-description">Nama atau model kendaraan.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Nomor polisi</span>
                        <strong class="tms-info-value">{{ $vehicle->plate_number }}</strong>
                        <span class="tms-info-description">Identitas kendaraan saat ini.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Kapasitas</span>
                        <strong class="tms-info-value">{{ $vehicle->capacity }} pax</strong>
                        <span class="tms-info-description">Kapasitas penumpang.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Status</span>
                        <strong class="tms-info-value">{{ $vehicle->status }}</strong>
                        <span class="tms-info-description">Status operasional saat ini.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Dipakai pada layanan</span>
                        <strong class="tms-info-value">{{ number_format($serviceCount) }}</strong>
                        <span class="tms-info-description">Jumlah layanan dengan kendaraan ini.</span>
                    </div>
                </div>
                <div class="tms-info-panel-footer">
                    <a href="{{ route('vehicles.index') }}">&#8592; Kembali ke daftar kendaraan</a>
                </div>
            </aside>
        </div>
    </div>
@endsection
