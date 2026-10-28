@extends('layouts.app')

@section('title', 'Drivers')

@section('content')
    <header>
        {{-- Header --}}
        <div class="main-header">
            <div>
                <div class="service-create-eyebrow">
                    <span></span>
                    TRANSPORT OPERATIONS
                </div>
                <h1>Manajemen Driver</h1>
                <p>Kelola data pengemudi, informasi SIM, dan status operasional driver.</p>
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
                <strong>Data driver dan kesiapan operasional</strong>
                <p>Pastikan informasi pengemudi akurat. Hanya driver berstatus aktif yang seharusnya dipilih untuk
                    penugasan operasional.</p>
            </div>
        </div>
    </header>
    <div class="service-create-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
                

                {{-- Form Tambah Driver --}}
                <form id="driver-form" method="POST" action="{{ route('drivers.store') }}" class="service-create-form">
                    @csrf

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
                                <h2>Tambah Driver</h2>
                                <p>Masukkan informasi pengemudi baru.</p>
                            </div>
                            <span class="service-create-step">01</span>
                        </div>

                        <div class="service-create-fields">
                            <div class="service-create-field">
                                <label for="name">Nama Driver <span>*</span></label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}"
                                    placeholder="Masukkan nama lengkap" autocomplete="name" required>
                                @error('name')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="phone">Nomor Telepon</label>
                                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                                    placeholder="Contoh: +62 812..." autocomplete="tel">
                                @error('phone')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="license_number">Nomor SIM</label>
                                <input id="license_number" name="license_number" type="text"
                                    value="{{ old('license_number') }}" placeholder="Masukkan nomor SIM">
                                @error('license_number')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="notes">Catatan</label>
                                <input id="notes" name="notes" type="text" value="{{ old('notes') }}"
                                    placeholder="Catatan tambahan (opsional)">
                                @error('notes')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="service-itinerary-info" style="margin-top: 20px;">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6" />
                                <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            <label
                                style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer; margin: 0;">
                                <input type="checkbox" name="is_active" value="1" style="width: auto; margin: 0;"
                                    @checked(old('is_active', '1') == '1')>
                                <span>
                                    <strong>Driver Aktif</strong>
                                    <small style="display: block;">Driver dapat dipilih untuk penugasan operasional.</small>
                                </span>
                            </label>
                        </div>

                        <div class="service-create-actions">
                            <button type="submit" class="service-submit-btn">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                </svg>
                                Simpan Driver
                            </button>
                        </div>
                    </section>
                </form>

                {{-- Daftar Driver --}}
                <section class="service-create-card" style="margin-top: 24px;">
                    <div class="service-create-section-header">
                        <div class="service-create-section-icon service-icon-green">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="9" cy="8" r="3.5" stroke="currentColor"
                                    stroke-width="1.7" />
                                <path d="M2.5 20v-1.5A6.5 6.5 0 0 1 9 12h1" stroke="currentColor" stroke-width="1.7"
                                    stroke-linecap="round" />
                                <path d="M16 12a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" stroke="currentColor" stroke-width="1.7" />
                                <path d="M16 10v2m0 6v2m-6-5h3m6 0h2" stroke="currentColor" stroke-width="1.7"
                                    stroke-linecap="round" />
                            </svg>
                        </div>
                        <div>
                            <h2>Daftar Driver</h2>
                            <p>Informasi pengemudi yang terdaftar di sistem.</p>
                        </div>
                        <span class="service-create-step">{{ $drivers->total() }}</span>
                    </div>

                    <div class="service-create-fields" style="margin-bottom: 20px;">
                        <div class="service-create-field">
                            <label>Total Driver</label>
                            <input type="text" value="{{ $drivers->total() }} driver terdaftar" readonly>
                        </div>
                        <div class="service-create-field">
                            <label>Halaman</label>
                            <input type="text" value="{{ $drivers->currentPage() }} dari {{ $drivers->lastPage() }}"
                                readonly>
                        </div>
                    </div>

                    <div class="tablewrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama Driver</th>
                                    <th>Telepon</th>
                                    <th>Nomor SIM</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($drivers as $d)
                                    <tr>
                                        <td><strong>{{ $d->name }}</strong></td>
                                        <td>{{ $d->phone ?: '—' }}</td>
                                        <td>{{ $d->license_number ?: '—' }}</td>
                                        <td>
                                            <span class="badge">
                                                {{ $d->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 28px;">
                                            Belum ada driver terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($drivers->hasPages())
                        <div style="margin-top: 20px;">
                            {{ $drivers->links() }}
                        </div>
                    @endif
                </section>
            </div>

            <aside class="tms-info-panel" aria-label="Informasi Drivers">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Drivers</h2>
                    <p>Ringkasan data driver yang terdaftar.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Total driver</span>
                        <strong class="tms-info-value">{{ number_format($driverStats['total']) }}</strong>
                        <span class="tms-info-description">Seluruh driver dalam sistem.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Aktif</span>
                        <strong class="tms-info-value">{{ number_format($driverStats['active']) }}</strong>
                        <span class="tms-info-description">Driver berstatus aktif.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Nonaktif</span>
                        <strong class="tms-info-value">{{ number_format($driverStats['inactive']) }}</strong>
                        <span class="tms-info-description">Driver yang tidak aktif.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Nomor SIM terisi</span>
                        <strong class="tms-info-value">{{ number_format($driverStats['with_license']) }}</strong>
                        <span class="tms-info-description">Data nomor SIM yang telah diisi.</span>
                    </div>
                </div>
                <div class="tms-info-panel-footer"><a href="#driver-form">Tambah driver melalui formulir</a></div>
            </aside>
        </div>
    </div>
@endsection
