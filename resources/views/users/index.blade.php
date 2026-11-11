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
                @php
                    $editingReservationUser = $reservationUsers->firstWhere('id', old('user_id'));
                @endphp
                <div class="tms-modal" id="reservationUserModal" hidden
                    data-open="{{ $errors->any() ? '1' : '0' }}"
                    data-context="{{ old('user_context', 'create') }}"
                    data-user-id="{{ old('user_id') }}"
                    data-edit-action="{{ $editingReservationUser ? route('reservation-users.update', $editingReservationUser) : '' }}">
                    <div class="tms-modal-backdrop" data-reservation-user-modal-close></div>
                    <div class="tms-modal-dialog" role="dialog" aria-modal="true"
                        aria-labelledby="reservationUserModalTitle">
                        <div class="tms-modal-header">
                            <div>
                                <h2 id="reservationUserModalTitle">Tambah User Reservation</h2>
                                <p id="reservationUserModalDescription">Masukkan data akun staf Reservasi baru.</p>
                            </div>
                            <button type="button" class="tms-modal-close" data-reservation-user-modal-close
                                aria-label="Tutup">&times;</button>
                        </div>
                <form id="reservation-user-form" method="POST" action="{{ route('reservation-users.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="reservationUserMethod" value="PUT" disabled>
                    <input type="hidden" name="user_context" id="reservationUserContext"
                        value="{{ old('user_context', 'create') }}">
                    <input type="hidden" name="user_id" id="reservationUserId" value="{{ old('user_id') }}">

                    <div class="tms-modal-body">
                        <div class="service-create-fields">
                            <div class="service-create-field">
                                <label for="reservationUserName">Nama Lengkap <span>*</span></label>
                                <input id="reservationUserName" name="name" type="text" value="{{ old('name') }}"
                                    placeholder="Masukkan nama lengkap" autocomplete="name" required>
                                @error('name')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="reservationUserEmail">Email <span>*</span></label>
                                <input id="reservationUserEmail" name="email" type="email" value="{{ old('email') }}"
                                    placeholder="Contoh: reservasi@balikamitour.com" autocomplete="email" required>
                                @error('email')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="reservationUserPhone">Nomor Telepon</label>
                                <input id="reservationUserPhone" name="phone" type="tel" value="{{ old('phone') }}"
                                    placeholder="Contoh: +62 812..." autocomplete="tel">
                                @error('phone')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="reservationUserPassword">Password <span class="reservation-user-password-required">*</span></label>
                                <input id="reservationUserPassword" name="password" type="password" placeholder="Minimal 8 karakter"
                                    autocomplete="new-password" required>
                                @error('password')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="reservationUserPasswordConfirmation">Konfirmasi Password <span class="reservation-user-password-required">*</span></label>
                                <input id="reservationUserPasswordConfirmation" name="password_confirmation" type="password"
                                    placeholder="Ulangi password" autocomplete="new-password" required>
                            </div>
                        </div>

                        <div class="service-itinerary-info">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6" />
                                <path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            <label
                                style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer; margin: 0;">
                                <input type="hidden" name="is_active" value="0">
                                <input id="reservationUserActive" type="checkbox" name="is_active" value="1" style="width: auto; margin: 0;"
                                    @checked(old('is_active', '1') == '1')>
                                <span>
                                    <strong>Akun Aktif</strong>
                                    <small style="display: block;">User dapat login dan mengirim permintaan layanan.</small>
                                </span>
                            </label>
                        </div>

                    </div>
                    <div class="tms-modal-footer">
                            <button type="button" class="tms-modal-btn tms-modal-btn-secondary"
                                data-reservation-user-modal-close>Batal</button>
                            <button type="submit" class="tms-modal-btn tms-modal-btn-primary"
                                id="reservationUserSubmit">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                </svg>
                                Simpan User Reservation
                            </button>
                        </div>
                </form>
                    </div>
                </div>

                <section class="service-create-card">
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
                        <button type="button" class="tms-modal-btn tms-modal-btn-primary"
                            data-reservation-user-create-open>Tambah User</button>
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
                                            <button type="button" class="services-detail-btn"
                                                data-reservation-user-edit-open
                                                data-user-action="{{ route('reservation-users.update', $user) }}"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->name }}"
                                                data-user-email="{{ $user->email }}"
                                                data-user-phone="{{ $user->phone }}"
                                                data-user-active="{{ $user->is_active ? '1' : '0' }}">Edit</button>
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
                <div class="tms-info-panel-footer">
                    <button type="button" class="tms-modal-btn tms-modal-btn-primary"
                        data-reservation-user-create-open>Tambah user</button>
                </div>
            </aside>
        </div>
    </div>
    <script>
        (() => {
            const modal = document.getElementById('reservationUserModal');
            const form = document.getElementById('reservation-user-form');

            if (!modal || !form) return;

            const method = document.getElementById('reservationUserMethod');
            const context = document.getElementById('reservationUserContext');
            const userId = document.getElementById('reservationUserId');
            const name = document.getElementById('reservationUserName');
            const email = document.getElementById('reservationUserEmail');
            const phone = document.getElementById('reservationUserPhone');
            const password = document.getElementById('reservationUserPassword');
            const passwordConfirmation = document.getElementById('reservationUserPasswordConfirmation');
            const active = document.getElementById('reservationUserActive');
            const title = document.getElementById('reservationUserModalTitle');
            const description = document.getElementById('reservationUserModalDescription');
            const submit = document.getElementById('reservationUserSubmit');
            const passwordRequiredMarkers = modal.querySelectorAll('.reservation-user-password-required');

            const openModal = () => {
                modal.hidden = false;
                document.body.classList.add('tms-modal-open');
                window.setTimeout(() => name.focus(), 30);
            };

            const closeModal = () => {
                modal.hidden = true;
                document.body.classList.remove('tms-modal-open');
            };

            const setCreateForm = () => {
                form.reset();
                form.action = @json(route('reservation-users.store'));
                method.disabled = true;
                context.value = 'create';
                userId.value = '';
                password.required = true;
                passwordConfirmation.required = true;
                passwordRequiredMarkers.forEach((marker) => marker.hidden = false);
                active.checked = true;
                title.textContent = 'Tambah User Reservation';
                description.textContent = 'Masukkan data akun staf Reservasi baru.';
                submit.textContent = 'Simpan User Reservation';
            };

            const setEditForm = (trigger) => {
                form.action = trigger.dataset.userAction;
                method.disabled = false;
                context.value = 'edit';
                userId.value = trigger.dataset.userId;
                name.value = trigger.dataset.userName;
                email.value = trigger.dataset.userEmail;
                phone.value = trigger.dataset.userPhone || '';
                password.value = '';
                passwordConfirmation.value = '';
                password.required = false;
                passwordConfirmation.required = false;
                passwordRequiredMarkers.forEach((marker) => marker.hidden = true);
                active.checked = trigger.dataset.userActive === '1';
                title.textContent = 'Edit User Reservation';
                description.textContent = 'Perbarui data akun. Kosongkan password jika tidak ingin menggantinya.';
                submit.textContent = 'Simpan Perubahan';
            };

            document.querySelectorAll('[data-reservation-user-create-open]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    setCreateForm();
                    openModal();
                });
            });

            document.querySelectorAll('[data-reservation-user-edit-open]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    setEditForm(trigger);
                    openModal();
                });
            });

            modal.querySelectorAll('[data-reservation-user-modal-close]').forEach((element) => {
                element.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.hidden) closeModal();
            });

            if (modal.dataset.open === '1') {
                const isEdit = modal.dataset.context === 'edit' && modal.dataset.editAction;
                if (isEdit) {
                    form.action = modal.dataset.editAction;
                    method.disabled = false;
                    password.required = false;
                    passwordConfirmation.required = false;
                    passwordRequiredMarkers.forEach((marker) => marker.hidden = true);
                    title.textContent = 'Edit User Reservation';
                    description.textContent = 'Perbarui data akun. Kosongkan password jika tidak ingin menggantinya.';
                    submit.textContent = 'Simpan Perubahan';
                } else {
                    method.disabled = true;
                }

                openModal();
            }
        })();
    </script>
@endsection
