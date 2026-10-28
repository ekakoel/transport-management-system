@extends('layouts.app')

@section('title', 'Emergency Contacts')

@section('content')
<header>
    {{-- Header --}}
        <div class="main-header">
            <div>
                <div class="service-create-eyebrow">
                    <span></span>
                    TRANSPORT OPERATIONS
                </div>
                <h1>Kontak Darurat</h1>
                <p>Kelola kontak operasional yang ditampilkan pada Surat Perintah Kerja (SPK) driver.</p>
            </div>
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
                <strong>Pastikan kontak dapat dihubungi</strong>
                <p>Gunakan nomor telepon dan WhatsApp yang aktif. Kontak default akan digunakan sesuai mekanisme aplikasi ketika tidak ada kontak khusus yang dipilih.</p>
            </div>
        </div>
</header>
    <div class="service-create-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
        

        {{-- Form Tambah Kontak --}}
        <form id="contact-form" method="POST" action="{{ route('contacts.store') }}" class="service-create-form">
            @csrf

            <section class="service-create-card">
                <div class="service-create-section-header">
                    <div class="service-create-section-icon service-icon-blue">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M22 16.9v3a2 2 0 0 1-2.2 2
                                19.8 19.8 0 0 1-8.6-3.1
                                19.4 19.4 0 0 1-6-6
                                19.8 19.8 0 0 1-3.1-8.6
                                A2 2 0 0 1 4.1 1h3
                                a2 2 0 0 1 2 1.7
                                c.1 1.1.4 2.1.7 3.1
                                a2 2 0 0 1-.5 2.1L8 9.2
                                a16 16 0 0 0 6 6l1.3-1.3
                                a2 2 0 0 1 2.1-.5
                                c1 .3 2 .6 3.1.7
                                a2 2 0 0 1 1.5 2.8Z"
                                stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Tambah Kontak Darurat</h2>
                        <p>Masukkan informasi tim atau petugas operasional.</p>
                    </div>
                    <span class="service-create-step">01</span>
                </div>

                <div class="service-create-fields">
                    <div class="service-create-field">
                        <label for="name">Nama Tim / Petugas <span>*</span></label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Operasional Bali Kami Tour"
                            required
                        >
                        @error('name')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="service-create-field">
                        <label for="phone">Nomor Telepon <span>*</span></label>
                        <input
                            id="phone"
                            type="tel"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="Contoh: +62 812..."
                            autocomplete="tel"
                            required
                        >
                        @error('phone')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="service-create-field service-field-full">
                        <label for="whatsapp">Nomor WhatsApp</label>
                        <input
                            id="whatsapp"
                            type="tel"
                            name="whatsapp"
                            value="{{ old('whatsapp') }}"
                            placeholder="Contoh: 628123456789"
                        >
                        @error('whatsapp')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                        <small class="service-create-help">
                            Masukkan nomor WhatsApp dengan kode negara jika tersedia.
                        </small>
                    </div>
                </div>

                {{-- Contact Settings --}}
                <div class="service-itinerary-info" style="margin-top: 20px;">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/>
                        <path d="m8 12 2.5 2.5L16 9" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round"/>
                    </svg>
                    <div style="display: grid; gap: 14px;">
                        <label style="display: inline-flex; align-items: flex-start; gap: 10px; cursor: pointer; margin: 0;">
                            <input
                                type="checkbox"
                                name="is_default"
                                value="1"
                                style="width: auto; margin: 3px 0 0;"
                                @checked(old('is_default'))
                            >
                            <span>
                                <strong>Jadikan Kontak Default</strong>
                                <small style="display: block;">Tetapkan sebagai kontak default sesuai aturan aplikasi.</small>
                            </span>
                        </label>

                        <label style="display: inline-flex; align-items: flex-start; gap: 10px; cursor: pointer; margin: 0;">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                style="width: auto; margin: 3px 0 0;"
                                @checked(old('is_active', '1') == '1')
                            >
                            <span>
                                <strong>Kontak Aktif</strong>
                                <small style="display: block;">Tandai jika kontak dapat digunakan untuk keperluan operasional.</small>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="service-create-actions">
                    <button type="submit" class="service-submit-btn">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Tambah Kontak
                    </button>
                </div>
            </section>
        </form>

        {{-- Daftar Kontak --}}
        <section class="service-create-card" style="margin-top: 24px;">
            <div class="service-create-section-header">
                <div class="service-create-section-icon service-icon-green">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M22 16.9v3a2 2 0 0 1-2.2 2
                            19.8 19.8 0 0 1-8.6-3.1
                            19.4 19.4 0 0 1-6-6
                            19.8 19.8 0 0 1-3.1-8.6
                            A2 2 0 0 1 4.1 1h3"
                            stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 2h7v7m0-7-9 9"
                            stroke="currentColor" stroke-width="1.7"
                            stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <h2>Daftar Kontak</h2>
                    <p>Kontak operasional yang terdaftar di sistem.</p>
                </div>
                <span class="service-create-step">{{ $contacts->count() }}</span>
            </div>

            <div class="tablewrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Tim / Petugas</th>
                            <th>Telepon</th>
                            <th>WhatsApp</th>
                            <th>Default</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($contacts as $c)
                            <tr>
                                <td><strong>{{ $c->name }}</strong></td>
                                <td>{{ $c->phone }}</td>
                                <td>{{ $c->whatsapp ?: '—' }}</td>
                                <td>
                                    <span class="badge">
                                        {{ $c->is_default ? 'Ya' : '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge">
                                        {{ $c->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 28px;">
                                    Belum ada kontak terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
            </div>

            <aside class="tms-info-panel" aria-label="Informasi Kontak Darurat">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Kontak Darurat</h2>
                    <p>Ringkasan kontak untuk kebutuhan operasional SPK.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Total kontak</span>
                        <strong class="tms-info-value">{{ number_format($contactStats['total']) }}</strong>
                        <span class="tms-info-description">Seluruh kontak tersimpan.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Aktif</span>
                        <strong class="tms-info-value">{{ number_format($contactStats['active']) }}</strong>
                        <span class="tms-info-description">Kontak yang aktif.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Nonaktif</span>
                        <strong class="tms-info-value">{{ number_format($contactStats['inactive']) }}</strong>
                        <span class="tms-info-description">Kontak yang tidak aktif.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Kontak default</span>
                        <strong class="tms-info-value">{{ number_format($contactStats['default']) }}</strong>
                        <span class="tms-info-description">Kontak default yang ditandai pada sistem.</span>
                    </div>
                </div>
                <div class="tms-info-panel-footer"><a href="#contact-form">Tambah kontak melalui formulir</a></div>
            </aside>
        </div>
    </div>
@endsection
