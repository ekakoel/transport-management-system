@extends('layouts.app')

@section('title', 'Profil Saya')

@php
    $accountInitials = collect(preg_split('/\s+/', trim($user->name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
    $roleLabel = ucfirst(str_replace('_', ' ', $user->role ?: 'user'));
@endphp

@section('content')
    <header>
        <div class="main-header">
            <div>
                <div class="service-create-eyebrow">
                    <span></span>
                    ACCOUNT SETTINGS
                </div>
                <h1>Profil Saya</h1>
                <p>Kelola informasi akun dan keamanan akses Anda ke Bali Kami TMS.</p>
            </div>
        </div>
    </header>

    <div class="service-create-page profile-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
                <form method="POST" action="{{ route('profile.update') }}" class="service-create-form">
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
                                <h2>Informasi Akun</h2>
                                <p>Perbarui data diri yang digunakan pada akun Anda.</p>
                            </div>
                            <span class="service-create-step">01</span>
                        </div>

                        <div class="service-create-fields">
                            <div class="service-create-field">
                                <label for="name">Nama Lengkap <span>*</span></label>
                                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                                    placeholder="Masukkan nama lengkap" autocomplete="name" maxlength="150" required>
                                @error('name')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="email">Email <span>*</span></label>
                                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                                    placeholder="Contoh: nama@balikamitour.com" autocomplete="email" maxlength="190"
                                    required>
                                @error('email')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="phone">Nomor Telepon</label>
                                <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}"
                                    placeholder="Contoh: +62 812..." autocomplete="tel" maxlength="40">
                                @error('phone')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="service-create-card">
                        <div class="service-create-section-header">
                            <div class="service-create-section-icon service-icon-blue">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="4" y="10" width="16" height="11" rx="2.5" stroke="currentColor"
                                        stroke-width="1.7" />
                                    <path d="M8 10V7a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" />
                                    <path d="M12 14v3" stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" />
                                </svg>
                            </div>
                            <div>
                                <h2>Keamanan Akun</h2>
                                <p>Kosongkan bagian ini jika tidak ingin mengganti password.</p>
                            </div>
                            <span class="service-create-step">02</span>
                        </div>

                        <div class="service-create-fields">
                            <div class="service-create-field">
                                <label for="current_password">Password Saat Ini</label>
                                <input id="current_password" name="current_password" type="password"
                                    placeholder="Masukkan password saat ini" autocomplete="current-password">
                                @error('current_password')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="password">Password Baru</label>
                                <input id="password" name="password" type="password"
                                    placeholder="Minimal 8 karakter" autocomplete="new-password">
                                <small class="service-create-help">Isi hanya jika ingin mengganti password akun.</small>
                                @error('password')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="password_confirmation">Konfirmasi Password Baru</label>
                                <input id="password_confirmation" name="password_confirmation" type="password"
                                    placeholder="Ulangi password baru" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="profile-password-note">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7" />
                                <path d="M12 11v5m0-8h.01" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg>
                            <span>Demi keamanan akun, memasukkan password baru wajib disertai password saat ini.</span>
                        </div>

                        <div class="service-create-actions">
                            <button type="submit" class="service-submit-btn">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m5 12 4 4L19 6" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('dashboard') }}" class="service-cancel-btn">Batal</a>
                        </div>
                    </section>
                </form>
            </div>

            <aside class="tms-info-panel" aria-label="Ringkasan Profil">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">PROFIL ANDA</span>
                    <h2>Ringkasan Akun</h2>
                    <p>Informasi akun yang sedang digunakan untuk masuk ke sistem.</p>
                </div>

                <div class="profile-identity">
                    <span class="profile-avatar" aria-hidden="true">{{ $accountInitials ?: 'U' }}</span>
                    <div class="profile-identity-copy">
                        <h3>{{ $user->name }}</h3>
                        <span class="profile-role-badge">{{ $roleLabel }}</span>
                    </div>
                </div>

                <div class="profile-summary-list">
                    <div class="profile-summary-row">
                        <span class="profile-summary-label">Email</span>
                        <span class="profile-summary-value">{{ $user->email }}</span>
                    </div>
                    <div class="profile-summary-row">
                        <span class="profile-summary-label">Telepon</span>
                        <span class="profile-summary-value">{{ $user->phone ?: 'Belum diatur' }}</span>
                    </div>
                    <div class="profile-summary-row">
                        <span class="profile-summary-label">Status</span>
                        <span class="profile-summary-value profile-status">
                            <span class="profile-status-dot {{ $user->is_active ? '' : 'is-inactive' }}"></span>
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div class="profile-summary-row">
                        <span class="profile-summary-label">Terdaftar sejak</span>
                        <span
                            class="profile-summary-value">{{ \App\Support\DateFormatter::format($user->created_at) }}</span>
                    </div>
                    @if (strtolower((string) $user->role) === 'reservation')
                        <div class="profile-summary-row">
                            <span class="profile-summary-label">Permintaan layanan</span>
                            <span class="profile-summary-value">{{ number_format($reservationCount) }}</span>
                        </div>
                    @endif
                </div>

                <div class="tms-info-panel-footer">
                    <a href="{{ route('dashboard') }}">&#8592; Kembali ke dashboard</a>
                </div>
            </aside>
        </div>
    </div>
@endsection
