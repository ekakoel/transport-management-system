@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
    <header>
        <div class="main-header">
            <div>
                <div class="service-create-eyebrow">
                    <span></span>
                    TRANSPORT OPERATIONS
                </div>
                <h1>Manajemen User</h1>
                <p>Kelola akun pengguna, role, dan status akses pada sistem.</p>
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
                <strong>Akun Pengguna Sistem</strong>
                <p>Admin dapat menambah, mengubah role, menonaktifkan, dan menghapus akun pengguna. Role menentukan
                    fitur yang dapat diakses setiap pengguna.</p>
            </div>
        </div>

        @if ($errors->has('user'))
            <div class="main-notice my-2" role="alert">
                <span class="main-notice-icon">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7" />
                        <path d="M12 8v5m0 3h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </span>
                <div>
                    <strong>Perubahan tidak dapat diproses</strong>
                    <p>{{ $errors->first('user') }}</p>
                </div>
            </div>
        @endif
    </header>

    <div class="service-create-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
                <div class="tms-modal" id="userManagerModal" hidden
                    data-open="{{ old('user_context') ? '1' : '0' }}"
                    data-context="{{ old('user_context', 'create') }}"
                    data-user-id="{{ old('user_id') }}"
                    data-edit-action="{{ $editingUser ? route('users.update', $editingUser) : '' }}">
                    <div class="tms-modal-backdrop" data-user-modal-close></div>
                    <div class="tms-modal-dialog" role="dialog" aria-modal="true"
                        aria-labelledby="userManagerModalTitle">
                        <div class="tms-modal-header">
                            <div>
                                <h2 id="userManagerModalTitle">Tambah User</h2>
                                <p id="userManagerModalDescription">Masukkan data akun pengguna baru.</p>
                            </div>
                            <button type="button" class="tms-modal-close" data-user-modal-close
                                aria-label="Tutup">&times;</button>
                        </div>
                        <form id="user-manager-form" method="POST" action="{{ route('users.store') }}">
                            @csrf
                            <input type="hidden" name="_method" id="userMethod" value="PUT" disabled>
                            <input type="hidden" name="user_context" id="userContext"
                                value="{{ old('user_context', 'create') }}">
                            <input type="hidden" name="user_id" id="userId" value="{{ old('user_id') }}">

                            <div class="tms-modal-body">
                                <div class="service-create-fields">
                                    <div class="service-create-field">
                                        <label for="userName">Nama Lengkap <span>*</span></label>
                                        <input id="userName" name="name" type="text" value="{{ old('name') }}"
                                            placeholder="Masukkan nama lengkap" autocomplete="name" required>
                                        @error('name')
                                            <small class="service-create-error">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="service-create-field">
                                        <label for="userEmail">Email <span>*</span></label>
                                        <input id="userEmail" name="email" type="email" value="{{ old('email') }}"
                                            placeholder="Contoh: nama@balikamitour.com" autocomplete="email" required>
                                        @error('email')
                                            <small class="service-create-error">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="service-create-field">
                                        <label for="userPhone">Nomor Telepon</label>
                                        <input id="userPhone" name="phone" type="tel" value="{{ old('phone') }}"
                                            placeholder="Contoh: +62 812..." autocomplete="tel">
                                        @error('phone')
                                            <small class="service-create-error">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="service-create-field">
                                        <label for="userRole">Role <span>*</span></label>
                                        <select id="userRole" name="role" required>
                                            @foreach ($roles as $roleValue => $roleLabel)
                                                <option value="{{ $roleValue }}" @selected(old('role', 'reservation') === $roleValue)>
                                                    {{ $roleLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                            <small class="service-create-error">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="service-create-field">
                                        <label for="userPassword">Password <span class="user-password-required">*</span></label>
                                        <input id="userPassword" name="password" type="password" placeholder="Minimal 8 karakter"
                                            autocomplete="new-password" required>
                                        @error('password')
                                            <small class="service-create-error">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="service-create-field">
                                        <label for="userPasswordConfirmation">Konfirmasi Password <span class="user-password-required">*</span></label>
                                        <input id="userPasswordConfirmation" name="password_confirmation" type="password"
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
                                        <input id="userActive" type="checkbox" name="is_active" value="1" style="width: auto; margin: 0;"
                                            @checked(old('is_active', '1') == '1')>
                                        <span>
                                            <strong>Akun Aktif</strong>
                                            <small style="display: block;">User dapat login dan mengakses sistem.</small>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="tms-modal-footer">
                                <button type="button" class="tms-modal-btn tms-modal-btn-secondary"
                                    data-user-modal-close>Batal</button>
                                <button type="submit" class="tms-modal-btn tms-modal-btn-primary" id="userSubmit">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" />
                                    </svg>
                                    Simpan User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="tms-modal" id="userDeleteModal" hidden>
                    <div class="tms-modal-backdrop" data-user-delete-close></div>
                    <div class="tms-modal-dialog tms-modal-dialog-sm" role="dialog" aria-modal="true"
                        aria-labelledby="userDeleteTitle">
                        <div class="tms-modal-header">
                            <h2 id="userDeleteTitle">Hapus User</h2>
                            <button type="button" class="tms-modal-close" data-user-delete-close
                                aria-label="Tutup">&times;</button>
                        </div>
                        <form id="userDeleteForm" method="POST" action="">
                            @csrf
                            @method('DELETE')
                            <div class="tms-modal-body">
                                <p>Hapus user <strong id="userDeleteName"></strong> dari sistem? Tindakan ini tidak dapat
                                    dibatalkan.</p>
                            </div>
                            <div class="tms-modal-footer">
                                <button type="button" class="tms-modal-btn tms-modal-btn-secondary"
                                    data-user-delete-close>Batal</button>
                                <button type="submit" class="tms-modal-btn tms-modal-btn-danger">Hapus User</button>
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
                            <h2>Daftar User</h2>
                            <p>Seluruh akun pengguna yang terdaftar di sistem.</p>
                        </div>
                        <span class="service-create-step">{{ $users->total() }}</span>
                        <button type="button" class="tms-modal-btn tms-modal-btn-primary"
                            data-user-create-open>Tambah User</button>
                    </div>

                    <div class="services-table-scroll">
                        <table class="services-table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Role</th>
                                    <th>Permintaan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td><strong>{{ $user->name }}</strong></td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone ?: '—' }}</td>
                                        <td>
                                            <span class="badge">{{ $roles[$user->role] ?? ucfirst((string) $user->role) }}</span>
                                        </td>
                                        <td>{{ number_format($user->reservations_count) }}</td>
                                        <td>
                                            <span class="badge">
                                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="services-detail-btn"
                                                data-user-edit-open
                                                data-user-action="{{ route('users.update', $user) }}"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->name }}"
                                                data-user-email="{{ $user->email }}"
                                                data-user-phone="{{ $user->phone }}"
                                                data-user-role="{{ $user->role }}"
                                                data-user-active="{{ $user->is_active ? '1' : '0' }}">Edit</button>
                                            <button type="button" class="services-detail-btn"
                                                data-user-delete-open
                                                data-user-action="{{ route('users.destroy', $user) }}"
                                                data-user-name="{{ $user->name }}"
                                                style="color: #b42318;">Hapus</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 28px;">
                                            Belum ada user. Gunakan tombol Tambah User untuk menambahkannya.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($users->hasPages())
                        <div class="tms-pagination-shell">
                            {{ $users->links() }}
                        </div>
                    @endif
                </section>
            </div>

            <aside class="tms-info-panel" aria-label="Informasi User">
                <div class="tms-info-panel-heading">
                    <span class="tms-info-panel-kicker">INFORMASI</span>
                    <h2>Manajemen User</h2>
                    <p>Ringkasan akun pengguna yang terdaftar.</p>
                </div>
                <div class="tms-info-list">
                    <div class="tms-info-item">
                        <span class="tms-info-label">Total user</span>
                        <strong class="tms-info-value">{{ number_format($userStats['total']) }}</strong>
                        <span class="tms-info-description">Seluruh akun dalam sistem.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Aktif</span>
                        <strong class="tms-info-value">{{ number_format($userStats['active']) }}</strong>
                        <span class="tms-info-description">Akun yang dapat login.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Nonaktif</span>
                        <strong class="tms-info-value">{{ number_format($userStats['inactive']) }}</strong>
                        <span class="tms-info-description">Akun yang tidak dapat login.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Admin / Owner / Manager</span>
                        <strong class="tms-info-value">{{ number_format($userStats['staff']) }}</strong>
                        <span class="tms-info-description">Akun dengan akses operasional.</span>
                    </div>
                    <div class="tms-info-item">
                        <span class="tms-info-label">Reservation</span>
                        <strong class="tms-info-value">{{ number_format($userStats['reservation']) }}</strong>
                        <span class="tms-info-description">Akun staf Reservasi.</span>
                    </div>
                </div>
                <div class="tms-info-panel-footer">
                    <button type="button" class="tms-modal-btn tms-modal-btn-primary"
                        data-user-create-open>Tambah user</button>
                </div>
            </aside>
        </div>
    </div>
    <script>
        (() => {
            const modal = document.getElementById('userManagerModal');
            const form = document.getElementById('user-manager-form');
            const deleteModal = document.getElementById('userDeleteModal');
            const deleteForm = document.getElementById('userDeleteForm');
            const deleteName = document.getElementById('userDeleteName');

            if (!modal || !form) return;

            const method = document.getElementById('userMethod');
            const context = document.getElementById('userContext');
            const userId = document.getElementById('userId');
            const name = document.getElementById('userName');
            const email = document.getElementById('userEmail');
            const phone = document.getElementById('userPhone');
            const role = document.getElementById('userRole');
            const password = document.getElementById('userPassword');
            const passwordConfirmation = document.getElementById('userPasswordConfirmation');
            const active = document.getElementById('userActive');
            const title = document.getElementById('userManagerModalTitle');
            const description = document.getElementById('userManagerModalDescription');
            const submit = document.getElementById('userSubmit');
            const passwordRequiredMarkers = modal.querySelectorAll('.user-password-required');

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
                form.action = @json(route('users.store'));
                method.disabled = true;
                context.value = 'create';
                userId.value = '';
                role.value = 'reservation';
                password.required = true;
                passwordConfirmation.required = true;
                passwordRequiredMarkers.forEach((marker) => marker.hidden = false);
                active.checked = true;
                title.textContent = 'Tambah User';
                description.textContent = 'Masukkan data akun pengguna baru.';
                submit.textContent = 'Simpan User';
            };

            const setEditForm = (trigger) => {
                form.action = trigger.dataset.userAction;
                method.disabled = false;
                context.value = 'edit';
                userId.value = trigger.dataset.userId;
                name.value = trigger.dataset.userName;
                email.value = trigger.dataset.userEmail;
                phone.value = trigger.dataset.userPhone || '';
                role.value = trigger.dataset.userRole || 'reservation';
                password.value = '';
                passwordConfirmation.value = '';
                window.resetPasswordVisibility?.(form);
                password.required = false;
                passwordConfirmation.required = false;
                passwordRequiredMarkers.forEach((marker) => marker.hidden = true);
                active.checked = trigger.dataset.userActive === '1';
                title.textContent = 'Edit User';
                description.textContent = 'Perbarui data akun. Kosongkan password jika tidak ingin menggantinya.';
                submit.textContent = 'Simpan Perubahan';
            };

            document.querySelectorAll('[data-user-create-open]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    setCreateForm();
                    openModal();
                });
            });

            document.querySelectorAll('[data-user-edit-open]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    setEditForm(trigger);
                    openModal();
                });
            });

            modal.querySelectorAll('[data-user-modal-close]').forEach((element) => {
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
                    title.textContent = 'Edit User';
                    description.textContent = 'Perbarui data akun. Kosongkan password jika tidak ingin menggantinya.';
                    submit.textContent = 'Simpan Perubahan';
                } else {
                    method.disabled = true;
                }

                openModal();
            }

            if (deleteModal && deleteForm) {
                const closeDeleteModal = () => {
                    deleteModal.hidden = true;
                    document.body.classList.remove('tms-modal-open');
                };

                document.querySelectorAll('[data-user-delete-open]').forEach((trigger) => {
                    trigger.addEventListener('click', () => {
                        deleteForm.action = trigger.dataset.userAction;
                        deleteName.textContent = trigger.dataset.userName;
                        deleteModal.hidden = false;
                        document.body.classList.add('tms-modal-open');
                    });
                });

                deleteModal.querySelectorAll('[data-user-delete-close]').forEach((element) => {
                    element.addEventListener('click', closeDeleteModal);
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && !deleteModal.hidden) closeDeleteModal();
                });
            }
        })();
    </script>
@endsection
