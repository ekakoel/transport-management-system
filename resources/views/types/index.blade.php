@extends('layouts.app')

@section('title', 'Service Types')

@section('content')
    <header>
        {{-- Header --}}
        <div class="main-header">
            <div>
                <div class="service-create-eyebrow">
                    <span></span>
                    TRANSPORT OPERATIONS
                </div>
                <h1>Manajemen Tipe Layanan</h1>
                <p>Kelola jenis layanan transportasi yang tersedia untuk kebutuhan operasional.</p>
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
                <strong>Kelola kategori layanan transportasi</strong>
                <p>Buat tipe layanan seperti Airport Transfer atau City Tour. Tipe yang aktif dapat digunakan sesuai alur
                    operasional aplikasi.</p>
            </div>
        </div>
    </header>
    <div class="service-create-page">
        <div class="tms-two-column">
            <div class="tms-main-column">


                {{-- Form Tambah Tipe Layanan --}}
                <form id="type-form" method="POST" action="{{ route('types.store') }}" class="service-create-form">
                    @csrf

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
                                <h2>Tambah Tipe Layanan</h2>
                                <p>Masukkan nama dan deskripsi jenis layanan baru.</p>
                            </div>
                            <span class="service-create-step">01</span>
                        </div>

                        <div class="service-create-fields">
                            <div class="service-create-field">
                                <label for="name">Nama Tipe Layanan <span>*</span></label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}"
                                    placeholder="Contoh: Airport Transfer" required>
                                @error('name')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="description">Deskripsi</label>
                                <input id="description" type="text" name="description" value="{{ old('description') }}"
                                    placeholder="Deskripsi singkat layanan">
                                @error('description')
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
                                    <strong>Tipe Layanan Aktif</strong>
                                    <small style="display: block;">Tipe layanan ditandai aktif untuk penggunaan
                                        operasional.</small>
                                </span>
                            </label>
                        </div>

                        <div class="service-create-actions">
                            <button type="submit" class="service-submit-btn">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                </svg>
                                Tambah Tipe Layanan
                            </button>
                        </div>
                    </section>
                </form>

                {{-- Daftar Tipe Layanan --}}
                <section class="service-create-card" style="margin-top: 24px;">
                    <div class="service-create-section-header">
                        <div class="service-create-section-icon service-icon-green">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 4h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Z"
                                    stroke="currentColor" stroke-width="1.7" />
                                <path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.7"
                                    stroke-linecap="round" />
                            </svg>
                        </div>
                        <div>
                            <h2>Daftar Tipe Layanan</h2>
                            <p>Jenis layanan transportasi yang terdaftar di sistem.</p>
                        </div>
                        <span class="service-create-step">{{ $types->count() }}</span>
                    </div>

                    <div class="tablewrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama Tipe Layanan</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($types as $t)
                                    <tr>
                                        <td><strong>{{ $t->name }}</strong></td>
                                        <td>{{ $t->description ?: '—' }}</td>
                                        <td>
                                            <span class="badge">
                                                {{ $t->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="text-align: center; padding: 28px;">
                                            Belum ada tipe layanan. Gunakan formulir di atas untuk menambahkannya.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <aside class="tms-info-panel" aria-label="Informasi Tipe Layanan">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Tipe Layanan</h2>
                    <p>Ringkasan tipe layanan yang dapat digunakan.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Total tipe</span>
                        <strong class="tms-info-value">{{ number_format($typeStats['total']) }}</strong>
                        <span class="tms-info-description">Seluruh tipe layanan.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Aktif</span>
                        <strong class="tms-info-value">{{ number_format($typeStats['active']) }}</strong>
                        <span class="tms-info-description">Tipe yang aktif.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Nonaktif</span>
                        <strong class="tms-info-value">{{ number_format($typeStats['inactive']) }}</strong>
                        <span class="tms-info-description">Tipe yang tidak aktif.</span>
                    </div>
                </div>
                <div class="tms-info-panel-footer"><a href="#type-form">Tambah tipe layanan melalui formulir</a></div>
            </aside>
        </div>
    </div>
@endsection
