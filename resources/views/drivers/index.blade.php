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
            <button type="button" class="services-create-btn" data-driver-create-open
                aria-haspopup="dialog" aria-controls="driverCreateModal">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                <span>Tambah Driver</span>
            </button>
        </div>
        {{-- Information Banner --}}
        <div class="main-notice mb-3">
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
                <div class="tms-modal" id="driverCreateModal"
                    data-open="{{ $errors->any() && old('driver_context') === 'create' ? '1' : '0' }}"
                    hidden>
                <div class="tms-modal-backdrop" data-driver-create-close></div>
                <div class="tms-modal-dialog" role="dialog" aria-modal="true"
                    aria-labelledby="driverCreateTitle">
                <form id="driver-form" method="POST" action="{{ route('drivers.store') }}">
                    @csrf
                    <input type="hidden" name="driver_context" value="create">


                        <div class="tms-modal-header">
                            <div class="service-create-section-icon service-icon-blue">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.7" />
                                    <path d="M4 21v-2a8 8 0 0 1 16 0v2" stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" />
                                </svg>
                            </div>
                            <div class="tms-modal-title">
                                <h2 id="driverCreateTitle">Tambah Driver</h2>
                                <p>Masukkan informasi pengemudi baru.</p>
                            </div>
                            <button type="button" class="tms-modal-close" data-driver-create-close
                                aria-label="Tutup">&times;</button>
                        </div>

                        <div class="tms-modal-body">
                            <div class="service-create-field">
                                <label for="driverCreateName">Nama Driver <span>*</span></label>
                                <input id="driverCreateName" name="name" type="text" value="{{ old('name') }}"
                                    placeholder="Masukkan nama lengkap" autocomplete="name" required>
                                @error('name')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="driverCreatePhone">Nomor Telepon</label>
                                <input id="driverCreatePhone" name="phone" type="tel" value="{{ old('phone') }}"
                                    placeholder="Contoh: +62 812..." autocomplete="tel">
                                @error('phone')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="driverCreateLicense">Nomor SIM</label>
                                <input id="driverCreateLicense" name="license_number" type="text"
                                    value="{{ old('license_number') }}" placeholder="Masukkan nomor SIM">
                                @error('license_number')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="service-create-field">
                                <label for="driverCreateNotes">Catatan</label>
                                <input id="driverCreateNotes" name="notes" type="text" value="{{ old('notes') }}"
                                    placeholder="Catatan tambahan (opsional)">
                                @error('notes')
                                    <small class="service-create-error">{{ $message }}</small>
                                @enderror
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
                                <input id="driverCreateActive" type="checkbox" name="is_active" value="1" style="width: auto; margin: 0;"
                                    @checked(old('is_active', '1') == '1')>
                                <span>
                                    <strong>Driver Aktif</strong>
                                    <small style="display: block;">Driver dapat dipilih untuk penugasan operasional.</small>
                                </span>
                            </label>
                        </div>

                        <div class="tms-modal-footer">
                            <button type="submit" class="service-submit-btn">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                </svg>
                                Simpan Driver
                            </button>
                        </div>
                </form>
                </div>
                </div>

                {{-- Filter Panel --}}
                <section class="services-filter-panel">
                    <div class="services-panel-heading">
                        <div class="services-panel-title">
                            <span class="services-filter-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 7h16M7 12h10m-7 5h4" stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" />
                                </svg>
                            </span>
                            <div>
                                <h2>Filter Driver</h2>
                                <p>Temukan driver berdasarkan nama, telepon, atau nomor SIM.</p>
                            </div>
                        </div>
                    </div>
                    <form method="GET" class="services-filter-form">
                        <div class="services-filter-field w-100">
                            <label for="driver-search">Nama / Telepon / Nomor SIM</label>
                            <div class="services-input-wrap">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor"
                                        stroke-width="1.7" />
                                    <path d="m16 16 4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                </svg>
                                <input type="search" id="driver-search" name="q" class="input-text"
                                    value="{{ request('q') }}" placeholder="Cari nama / telepon / SIM">
                            </div>
                        </div>
                        <div class="services-filter-field">
                            <label for="driver-status">Status Driver</label>
                            <select id="driver-status" name="status">
                                <option value="">Semua status</option>
                                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                            </select>
                        </div>
                        <button type="submit" class="services-filter-btn">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 7h16M7 12h10m-7 5h4" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" />
                            </svg>
                            Terapkan
                        </button>
                    </form>
                </section>

                {{-- Daftar Driver --}}
                <section class="service-create-card">
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

                    <div class="services-table-scroll">
                        <table class="services-table">
                            <thead>
                                <tr>
                                    <th>Nama Driver</th>
                                    <th>Telepon</th>
                                    <th>Nomor SIM</th>
                                    <th>Status</th>
                                    <th>Layanan</th>
                                    <th>Aksi</th>
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
                                        <td>{{ $d->services_count }}</td>
                                        <td>
                                            <div class="service-show-panel-actions">
                                                <button type="button" class="services-detail-btn"
                                                    data-driver-edit-open
                                                    data-driver-action="{{ route('drivers.update', $d) }}"
                                                    data-driver-id="{{ $d->id }}"
                                                    data-driver-name="{{ $d->name }}"
                                                    data-driver-phone="{{ $d->phone }}"
                                                    data-driver-license="{{ $d->license_number }}"
                                                    data-driver-notes="{{ $d->notes }}"
                                                    data-driver-active="{{ $d->is_active ? '1' : '0' }}">
                                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="m4 20 4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10L4 20Z"
                                                            stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
                                                        <path d="m13.5 5.5 3 3" stroke="currentColor" stroke-width="1.7"
                                                            stroke-linecap="round" />
                                                    </svg>
                                                    Edit
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 28px;">
                                            Belum ada driver terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($drivers->hasPages())
                        <div class="tms-pagination-shell">
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
                </div>
                <div class="tms-info-panel-footer">
                    <button type="button" class="tms-modal-btn tms-modal-btn-primary"
                        data-driver-create-open>+ Tambah driver</button>
                </div>
            </aside>
        </div>
    </div>

    <div class="tms-modal" id="driverEditModal" hidden
        data-open="{{ $errors->any() && old('driver_context') === 'edit' ? '1' : '0' }}"
        data-edit-action="{{ old('driver_id') ? route('drivers.update', old('driver_id')) : '' }}">
        <div class="tms-modal-backdrop" data-driver-edit-close></div>
        <div class="tms-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="driverEditTitle">
            <div class="tms-modal-header">
                <div class="service-create-section-icon service-icon-blue">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.7" />
                        <path d="M4 21v-2a8 8 0 0 1 16 0v2" stroke="currentColor" stroke-width="1.7"
                            stroke-linecap="round" />
                    </svg>
                </div>
                <div class="tms-modal-title">
                    <h2 id="driverEditTitle">Edit Driver</h2>
                    <p>Perbarui data pengemudi, informasi SIM, dan status operasional.</p>
                </div>
                <button type="button" class="tms-modal-close" data-driver-edit-close
                    aria-label="Tutup">&times;</button>
            </div>
            <form id="driverEditForm" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" name="driver_context" value="edit">
                <input type="hidden" name="driver_id" id="driverEditId" value="{{ old('driver_id') }}">
                <div class="tms-modal-body">
                    <div class="service-create-field">
                        <label for="driverEditName">Nama Driver <span>*</span></label>
                        <input id="driverEditName" name="name" type="text" maxlength="150"
                            value="{{ old('name') }}" autocomplete="name" required>
                        @error('name')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="service-create-field">
                        <label for="driverEditPhone">Nomor Telepon</label>
                        <input id="driverEditPhone" name="phone" type="tel" maxlength="40"
                            value="{{ old('phone') }}" autocomplete="tel">
                        @error('phone')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="service-create-field">
                        <label for="driverEditLicense">Nomor SIM</label>
                        <input id="driverEditLicense" name="license_number" type="text" maxlength="80"
                            value="{{ old('license_number') }}">
                        @error('license_number')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="service-create-field">
                        <label for="driverEditNotes">Catatan</label>
                        <input id="driverEditNotes" name="notes" type="text" maxlength="1000"
                            value="{{ old('notes') }}">
                        @error('notes')
                            <small class="service-create-error">{{ $message }}</small>
                        @enderror
                    </div>
                    <label style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer; margin: 0;">
                        <input type="hidden" name="is_active" value="0">
                        <input id="driverEditActive" type="checkbox" name="is_active" value="1"
                            style="width: auto; margin: 0;" @checked(old('is_active', '1') == '1')>
                        <span>
                            <strong>Driver Aktif</strong>
                            <small style="display: block;">Driver dapat dipilih untuk penugasan operasional.</small>
                        </span>
                    </label>
                </div>
                <div class="tms-modal-footer">
                    <button type="button" class="tms-modal-btn tms-modal-btn-secondary"
                        data-driver-edit-close>Batal</button>
                    <button type="submit" class="tms-modal-btn tms-modal-btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const createModal = document.getElementById('driverCreateModal');
            const editModal = document.getElementById('driverEditModal');
            const createForm = document.getElementById('driver-form');
            const editForm = document.getElementById('driverEditForm');

            if (!createModal || !editModal || !createForm || !editForm) return;

            const createName = document.getElementById('driverCreateName');
            const createPhone = document.getElementById('driverCreatePhone');
            const createLicense = document.getElementById('driverCreateLicense');
            const createNotes = document.getElementById('driverCreateNotes');
            const createActive = document.getElementById('driverCreateActive');
            const editName = document.getElementById('driverEditName');
            const editPhone = document.getElementById('driverEditPhone');
            const editLicense = document.getElementById('driverEditLicense');
            const editNotes = document.getElementById('driverEditNotes');
            const editActive = document.getElementById('driverEditActive');
            const syncBodyLock = () => {
                document.body.classList.toggle(
                    'tms-modal-open',
                    !createModal.hidden || !editModal.hidden
                );
            };

            const openModal = (modal, focusTarget) => {
                modal.hidden = false;
                syncBodyLock();
                window.setTimeout(() => focusTarget?.focus(), 30);
            };

            const closeModal = (modal) => {
                modal.hidden = true;
                syncBodyLock();
            };

            document.querySelectorAll('[data-driver-create-open]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    createForm.reset();
                    createActive.checked = true;
                    openModal(createModal, createName);
                });
            });

            document.querySelectorAll('[data-driver-edit-open]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    editForm.action = trigger.dataset.driverAction;
                    document.getElementById('driverEditId').value = trigger.dataset.driverId;
                    editName.value = trigger.dataset.driverName || '';
                    editPhone.value = trigger.dataset.driverPhone || '';
                    editLicense.value = trigger.dataset.driverLicense || '';
                    editNotes.value = trigger.dataset.driverNotes || '';
                    editActive.checked = trigger.dataset.driverActive === '1';
                    openModal(editModal, editName);
                });
            });

            createModal.querySelectorAll('[data-driver-create-close]').forEach((element) => {
                element.addEventListener('click', () => closeModal(createModal));
            });
            editModal.querySelectorAll('[data-driver-edit-close]').forEach((element) => {
                element.addEventListener('click', () => closeModal(editModal));
            });

            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') return;
                if (!editModal.hidden) closeModal(editModal);
                else if (!createModal.hidden) closeModal(createModal);
            });

            if (createModal.dataset.open === '1') openModal(createModal, createName);

            if (editModal.dataset.open === '1' && editModal.dataset.editAction) {
                editForm.action = editModal.dataset.editAction;
                openModal(editModal, editName);
            }
        })();
    </script>
@endsection
