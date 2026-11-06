@extends('layouts.app')

@section('title', 'Edit User Reservation')

@section('content')
    <header>
        <div class="main-header">
            <div>
                <div class="service-create-eyebrow">
                    <span></span>
                    TRANSPORT OPERATIONS
                </div>
                <h1>Edit User Reservation</h1>
                <p>Perbarui data akun, password, atau status akun Reservation.</p>
            </div>
        </div>
    </header>

    <div class="service-create-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
                <form id="reservation-user-edit-form" method="POST"
                    action="{{ route('reservation-users.update', $reservationUser) }}" class="service-create-form">
                    @csrf
                    @method('PUT')

                    <section class="service-create-card">
                        <div class="service-create-section-header">
                            <div class="service-create-section-icon service-icon-blue">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="9" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7" />
                                    <path d="M2.5 20v-1.5A6.5 6.5 0 0 1 9 12h1" stroke="currentColor"
                                        stroke-width="1.7" stroke-linecap="round" />
                                    <path d="M17 12v6m-3-3h6" stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" />
                                </svg>
                            </div>
                            <div>
                                <h2>{{ $reservationUser->name }}</h2>
                                <p>{{ $reservationUser->email }}</p>
                            </div>
                            <span class="service-create-step">01</span>
                        </div>

                        <div class="service-create-fields">
                            <div class="service-create-field">
                                <label for="name">Nama Lengkap <span>*</span></label>
                                <input id="name" name="name" type="text"
                                    value="{{ old('name', $reservationUser->name) }}" placeholder="Masukkan nama lengkap"
                                    autocomplete="name" required>
                                @error('name')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="email">Email <span>*</span></label>
                                <input id="email" name="email" type="email"
                                    value="{{ old('email', $reservationUser->email) }}"
                                    placeholder="Contoh: reservasi@balikamitour.com" autocomplete="email" required>
                                @error('email')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="phone">Nomor Telepon</label>
                                <input id="phone" name="phone" type="tel"
                                    value="{{ old('phone', $reservationUser->phone) }}" placeholder="Contoh: +62 812..."
                                    autocomplete="tel">
                                @error('phone')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="password">Password Baru</label>
                                <input id="password" name="password" type="password"
                                    placeholder="Kosongkan jika tidak diubah" autocomplete="new-password">
                                <small class="service-create-help">Isi minimal 8 karakter hanya jika ingin mengganti
                                    password.</small>
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

                        <div class="service-itinerary-info" style="margin-top: 20px;">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6" />
                                <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <label
                                style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer; margin: 0;">
                                <input type="checkbox" name="is_active" value="1" style="width: auto; margin: 0;"
                                    @checked(old('is_active', $reservationUser->is_active ? '1' : '0') == '1')>
                                <span>
                                    <strong>Akun Aktif</strong>
                                    <small style="display: block;">User dapat login dan mengirim permintaan layanan.</small>
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
                            <a href="{{ route('reservation-users.index') }}" class="service-cancel-btn">Batal</a>
                        </div>
                    </section>
                </form>
            </div>

            <aside class="tms-info-panel" aria-label="Informasi User Reservation">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Ringkasan Akun</h2>
                    <p>Informasi akun Reservasi yang sedang diubah.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Nama</span>
                        <strong class="tms-info-value">{{ $reservationUser->name }}</strong>
                        <span class="tms-info-description">Nama staf Reservation.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Email login</span>
                        <strong class="tms-info-value">{{ $reservationUser->email }}</strong>
                        <span class="tms-info-description">Digunakan untuk masuk ke sistem.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Status akun</span>
                        <strong class="tms-info-value">{{ $reservationUser->is_active ? 'Aktif' : 'Nonaktif' }}</strong>
                        <span class="tms-info-description">Status login akun saat ini.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Jumlah permintaan</span>
                        <strong
                            class="tms-info-value">{{ number_format($reservationUser->reservations()->count()) }}</strong>
                        <span class="tms-info-description">Permintaan layanan yang pernah dibuat.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Terdaftar sejak</span>
                        <strong
                            class="tms-info-value">{{ \App\Support\DateFormatter::format($reservationUser->created_at) }}</strong>
                        <span class="tms-info-description">Tanggal akun dibuat.</span>
                    </div>
                </div>
                <div class="tms-info-panel-footer">
                    <a href="{{ route('reservation-users.index') }}">&#8592; Kembali ke daftar user</a>
                </div>
            </aside>
        </div>
    </div>
@endsection
