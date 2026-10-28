@extends('layouts.app')

@section('title', 'Vehicles')

@section('content')
    <header>
        {{-- Header --}}
        <div class="main-header">
            <div>
                <div class="service-create-eyebrow">
                    <span></span>
                    TRANSPORT OPERATIONS
                </div>
                <h1>Manajemen Kendaraan</h1>
                <p>Kelola armada, nomor polisi, kapasitas penumpang, dan status operasional kendaraan.</p>
            </div>
        </div>
        {{-- Information Banner --}}
        <div class="main-notice">
            <span class="main-notice-icon">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7" />
                    <path d="M12 11v5m0-8h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </span>
            <div>
                <strong>Pastikan data armada selalu akurat</strong>
                <p>Perbarui informasi kendaraan dan status operasionalnya agar proses penugasan transportasi
                    berjalan dengan baik.</p>
            </div>
        </div>
    </header>
    <div class="service-create-page">
        <div class="tms-two-column">
            <div class="tms-main-column">




                {{-- Form Tambah Kendaraan --}}
                <form id="vehicle-form" method="POST" action="{{ route('vehicles.store') }}" class="service-create-form">
                    @csrf

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
                                <h2>Tambah Kendaraan</h2>
                                <p>Masukkan informasi kendaraan baru ke dalam sistem.</p>
                            </div>
                            <span class="service-create-step">01</span>
                        </div>

                        <div class="service-create-fields">
                            <div class="service-create-field">
                                <label for="name">Nama / Model Kendaraan <span>*</span></label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}"
                                    placeholder="Contoh: Toyota Innova Zenix" required>
                                @error('name')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="plate_number">Nomor Polisi <span>*</span></label>
                                <input id="plate_number" name="plate_number" type="text"
                                    value="{{ old('plate_number') }}" placeholder="Contoh: DK 1234 ABC" required>
                                @error('plate_number')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="capacity">Kapasitas Penumpang <span>*</span></label>
                                <input id="capacity" name="capacity" type="number" min="1" max="100"
                                    value="{{ old('capacity', 6) }}" placeholder="Jumlah penumpang" required>
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
                                    <option value="Available" @selected(old('status', 'Available') === 'Available')>
                                        Available
                                    </option>
                                    <option value="Assigned" @selected(old('status') === 'Assigned')>
                                        Assigned
                                    </option>
                                    <option value="Maintenance" @selected(old('status') === 'Maintenance')>
                                        Maintenance
                                    </option>
                                    <option value="Inactive" @selected(old('status') === 'Inactive')>
                                        Inactive
                                    </option>
                                </select>
                                @error('status')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field service-field-full">
                                <label for="notes">Catatan Kendaraan</label>
                                <textarea id="notes" name="notes" rows="3"
                                    placeholder="Informasi tambahan, kondisi kendaraan, atau catatan operasional...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="service-create-actions">
                            <button type="submit" class="service-submit-btn">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                </svg>
                                Simpan Kendaraan
                            </button>
                        </div>
                    </section>
                </form>

                {{-- Daftar Kendaraan --}}
                <section class="service-create-card" style="margin-top: 24px;">
                    <div class="service-create-section-header">
                        <div class="service-create-section-icon service-icon-green">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="m5 11 2-5h10l2 5M4 11h16v7H4z" stroke="currentColor" stroke-width="1.7"
                                    stroke-linejoin="round" />
                                <path d="M7 18v2m10-2v2" stroke="currentColor" stroke-width="1.7"
                                    stroke-linecap="round" />
                                <circle cx="7.5" cy="14.5" r="1.3" stroke="currentColor"
                                    stroke-width="1.5" />
                                <circle cx="16.5" cy="14.5" r="1.3" stroke="currentColor"
                                    stroke-width="1.5" />
                            </svg>
                        </div>
                        <div>
                            <h2>Daftar Kendaraan</h2>
                            <p>Informasi armada yang terdaftar dalam sistem.</p>
                        </div>
                        <span class="service-create-step">{{ $vehicles->total() }}</span>
                    </div>

                    <div class="service-create-fields" style="margin-bottom: 20px;">
                        <div class="service-create-field">
                            <label>Total Kendaraan</label>
                            <input type="text" value="{{ $vehicles->total() }} kendaraan terdaftar" readonly>
                        </div>
                        <div class="service-create-field">
                            <label>Halaman</label>
                            <input type="text"
                                value="{{ $vehicles->currentPage() }} dari {{ $vehicles->lastPage() }}" readonly>
                        </div>
                    </div>

                    <div class="tablewrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Kendaraan</th>
                                    <th>Nomor Polisi</th>
                                    <th>Kapasitas</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($vehicles as $v)
                                    <tr>
                                        <td><strong>{{ $v->name }}</strong></td>
                                        <td>{{ $v->plate_number }}</td>
                                        <td>{{ $v->capacity }} pax</td>
                                        <td>
                                            <span class="badge">{{ $v->status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 28px;">
                                            Belum ada kendaraan terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($vehicles->hasPages())
                        <div style="margin-top: 20px;">
                            {{ $vehicles->links() }}
                        </div>
                    @endif
                </section>
            </div>

            <aside class="tms-info-panel" aria-label="Informasi Kendaraan">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Kendaraan</h2>
                    <p>Ringkasan armada berdasarkan status saat ini.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Total kendaraan</span>
                        <strong class="tms-info-value">{{ number_format($vehicleStats['total']) }}</strong>
                        <span class="tms-info-description">Seluruh kendaraan terdaftar.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Available</span>
                        <strong class="tms-info-value">{{ number_format($vehicleStats['Available']) }}</strong>
                        <span class="tms-info-description">Kendaraan tersedia.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Assigned</span>
                        <strong class="tms-info-value">{{ number_format($vehicleStats['Assigned']) }}</strong>
                        <span class="tms-info-description">Kendaraan ditugaskan.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Maintenance</span>
                        <strong class="tms-info-value">{{ number_format($vehicleStats['Maintenance']) }}</strong>
                        <span class="tms-info-description">Kendaraan dalam perawatan.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Inactive</span>
                        <strong class="tms-info-value">{{ number_format($vehicleStats['Inactive']) }}</strong>
                        <span class="tms-info-description">Kendaraan nonaktif.</span>
                    </div>
                </div>
                <div class="tms-info-panel-footer"><a href="#vehicle-form">Tambah kendaraan melalui formulir</a></div>
            </aside>
        </div>
    </div>
@endsection
