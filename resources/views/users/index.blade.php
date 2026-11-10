@extends('layouts.app')

@section('title', 'Reservasi ')

@section('content')
    <header>
        <div class="main-header">
            <div>
                <div class="service-create-eyebrow">
                    <span></span>
                    TRANSPORT OPERATIONS
                </div>
                <h1>Manajemen User Reservation</h1>
                <p>Kelola akun staf Reservasi yang dapat membuat permintaan layanan transportasi.</p>
            </div>
        </div>

        <div class="main-notice my-2">
            <span class="main-notice-icon">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7" />
                    <path d="M12 11v5m0-8h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </span>
            <div>
                <strong>Akun untuk tim Reservation</strong>
                <p>User reservation dapat login dan mengirim permintaan layanan seperti yang sudah tersedia pada menu
                    Reservasi Saya. Gunakan email dan password yang valid saat membuat akun.</p>
            </div>
        </div>
    </header>

    <div class="service-create-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
                <form id="reservation-user-form" method="POST" action="{{ route('reservation-users.store') }}"
                    class="service-create-form">
                    @csrf

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
                                <h2>Tambah User Reservation</h2>
                                <p>Masukkan data akun staf Reservasi baru.</p>
                            </div>
                            <span class="service-create-step">01</span>
                        </div>

                        <div class="service-create-fields">
                            <div class="service-create-field">
                                <label for="name">Nama Lengkap <span>*</span></label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}"
                                    placeholder="Masukkan nama lengkap" autocomplete="name" required>
                                @error('name')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="email">Email <span>*</span></label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}"
                                    placeholder="Contoh: reservasi@balikamitour.com" autocomplete="email" required>
                                @error('email')
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
                                <label for="password">Password <span>*</span></label>
                                <input id="password" name="password" type="password" placeholder="Minimal 8 karakter"
                                    autocomplete="new-password" required>
                                @error('password')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="password_confirmation">Konfirmasi Password <span>*</span></label>
                                <input id="password_confirmation" name="password_confirmation" type="password"
                                    placeholder="Ulangi password" autocomplete="new-password" required>
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
                                    <strong>Akun Aktif</strong>
                                    <small style="display: block;">User dapat login dan mengirim permintaan layanan.</small>
                                </span>
                            </label>
                        </div>

                        <div class="service-create-actions">
                            <button type="submit" class="service-submit-btn">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                </svg>
                                Simpan User Reservation
                            </button>
                        </div>
                    </section>
                </form>

                <section class="service-create-card" style="margin-top: 24px;">
                    <div class="service-create-section-header">
                        <div class="service-create-section-icon service-icon-green">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="9" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7" />
                                <path d="M2.5 20v-1.5A6.5 6.5 0 0 1 9 12h1" stroke="currentColor"
                                    stroke-width="1.7" stroke-linecap="round" />
                                <path d="M16 12a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" stroke="currentColor"
                                    stroke-width="1.7" />
                                <path d="M16 10v2m0 6v2m-6-5h3m6 0h2" stroke="currentColor" stroke-width="1.7"
                                    stroke-linecap="round" />
                            </svg>
                        </div>
                        <div>
                            <h2>Daftar User Reservation</h2>
                            <p>Akun staf Reservasi yang terdaftar di sistem.</p>
                        </div>
                        <span class="service-create-step">{{ $reservationUsers->total() }}</span>
                    </div>

                    <div class="services-table-scroll">
                        <table class="services-table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Permintaan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($reservationUsers as $user)
                                    <tr>
                                        <td><strong>{{ $user->name }}</strong></td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone ?: '—' }}</td>
                                        <td>{{ number_format($user->reservations_count) }}</td>
                                        <td>
                                            <span class="badge">
                                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('reservation-users.edit', $user) }}"
                                                class="services-detail-btn">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 28px;">
                                            Belum ada user reservation. Gunakan formulir di atas untuk menambahkannya.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($reservationUsers->hasPages())
                        <div class="tms-pagination-shell">
                            {{ $reservationUsers->links() }}
                        </div>
                    @endif
                </section>
            </div>

            <aside class="tms-info-panel" aria-label="Informasi User Reservation">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>User Reservation</h2>
                    <p>Ringkasan akun staf Reservasi yang terdaftar.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Total user</span>
                        <strong class="tms-info-value">{{ number_format($reservationUserStats['total']) }}</strong>
                        <span class="tms-info-description">Seluruh akun Reservasi dalam sistem.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Aktif</span>
                        <strong class="tms-info-value">{{ number_format($reservationUserStats['active']) }}</strong>
                        <span class="tms-info-description">Akun yang dapat login dan membuat permintaan.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Nonaktif</span>
                        <strong class="tms-info-value">{{ number_format($reservationUserStats['inactive']) }}</strong>
                        <span class="tms-info-description">Akun yang tidak dapat login.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Pernah request</span>
                        <strong class="tms-info-value">{{ number_format($reservationUserStats['with_requests']) }}</strong>
                        <span class="tms-info-description">User yang sudah memiliki permintaan layanan.</span>
                    </div>
                </div>
                <div class="tms-info-panel-footer"><a href="#reservation-user-form">Tambah user melalui formulir</a>
                </div>
            </aside>
        </div>
    </div>
@endsection
