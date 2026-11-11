@extends('layouts.app')

@section('title', 'Tipe Layanan')

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
        <div class="main-notice mb-3">
            <span class="main-notice-icon">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7" />
                    <path d="M12 11v5m0-8h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </span>
            <div>
                <strong>Kelola kategori layanan transportasi</strong>
                <p>Buat tipe layanan seperti Airport Shuttle atau City Tour. Tipe yang aktif dapat digunakan sesuai alur
                    operasional aplikasi.</p>
            </div>
        </div>
    </header>
    <div class="service-create-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
                {{-- Daftar Tipe Layanan --}}
                <section class="service-create-card">
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
                        <button type="button" class="tms-modal-btn tms-modal-btn-primary"
                            data-type-create-open>Tambah Tipe</button>
                    </div>

                    <div class="services-table-scroll">
                        <table class="services-table">
                            <thead>
                                <tr>
                                    <th>Tipe Layanan</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
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
                                        <td>
                                            <button type="button" class="services-detail-btn"
                                                data-type-edit-open
                                                data-type-action="{{ route('types.update', $t) }}"
                                                data-type-id="{{ $t->id }}"
                                                data-type-name="{{ $t->name }}"
                                                data-type-description="{{ $t->description }}"
                                                data-type-active="{{ $t->is_active ? '1' : '0' }}">Edit</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 28px;">
                                            Belum ada tipe layanan. Gunakan tombol Tambah Tipe untuk membuatnya.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
                @php
                    $editingType = $types->firstWhere('id', old('type_id'));
                @endphp
                <div class="tms-modal" id="typeModal" hidden
                    data-open="{{ $errors->any() ? '1' : '0' }}"
                    data-context="{{ old('type_context', 'create') }}"
                    data-edit-action="{{ $editingType ? route('types.update', $editingType) : '' }}">
                    <div class="tms-modal-backdrop" data-type-modal-close></div>
                    <div class="tms-modal-dialog" role="dialog" aria-modal="true"
                        aria-labelledby="typeModalTitle">
                        <div class="tms-modal-header">
                            <div>
                                <h2 id="typeModalTitle">Tambah Tipe Layanan</h2>
                                <p id="typeModalDescription">Masukkan nama dan deskripsi jenis layanan baru.</p>
                            </div>
                            <button type="button" class="tms-modal-close" data-type-modal-close
                                aria-label="Tutup">&times;</button>
                        </div>
                        <form id="typeForm" method="POST" action="{{ route('types.store') }}">
                            @csrf
                            <input type="hidden" name="_method" id="typeMethod" value="PUT" disabled>
                            <input type="hidden" name="type_context" id="typeContext"
                                value="{{ old('type_context', 'create') }}">
                            <input type="hidden" name="type_id" id="typeId" value="{{ old('type_id') }}">
                            <div class="tms-modal-body">
                                <div class="service-create-field">
                                    <label for="typeName">Tipe Layanan <span>*</span></label>
                                    <input id="typeName" type="text" name="name" maxlength="120"
                                        value="{{ old('name') }}" placeholder="Contoh: Airport Shuttle" required>
                                    @error('name')
                                        <small class="service-create-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="service-create-field">
                                    <label for="typeDescription">Deskripsi</label>
                                    <input id="typeDescription" type="text" name="description" maxlength="500"
                                        value="{{ old('description') }}" placeholder="Deskripsi singkat layanan">
                                    @error('description')
                                        <small class="service-create-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <label
                                    style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer; margin: 0;">
                                    <input type="hidden" name="is_active" value="0">
                                    <input id="typeActive" type="checkbox" name="is_active" value="1"
                                        style="width: auto; margin: 0;"
                                        @checked(old('is_active', '1') == '1')>
                                    <span>
                                        <strong>Tipe Layanan Aktif</strong>
                                        <small style="display: block;">Tipe aktif dapat digunakan untuk kebutuhan operasional.</small>
                                    </span>
                                </label>
                            </div>
                            <div class="tms-modal-footer">
                                <button type="button" class="tms-modal-btn tms-modal-btn-secondary"
                                    data-type-modal-close>Batal</button>
                                <button type="submit" class="tms-modal-btn tms-modal-btn-primary" id="typeSubmit">
                                    Tambah Tipe Layanan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
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
                <div class="tms-info-panel-footer">
                    <button type="button" class="tms-modal-btn tms-modal-btn-primary"
                        data-type-create-open>Tambah tipe layanan</button>
                </div>
            </aside>
        </div>
    </div>
    <script>
        (() => {
            const modal = document.getElementById('typeModal');
            const form = document.getElementById('typeForm');

            if (!modal || !form) return;

            const method = document.getElementById('typeMethod');
            const context = document.getElementById('typeContext');
            const typeId = document.getElementById('typeId');
            const name = document.getElementById('typeName');
            const description = document.getElementById('typeDescription');
            const active = document.getElementById('typeActive');
            const title = document.getElementById('typeModalTitle');
            const modalDescription = document.getElementById('typeModalDescription');
            const submit = document.getElementById('typeSubmit');

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
                form.action = @json(route('types.store'));
                method.disabled = true;
                context.value = 'create';
                typeId.value = '';
                name.value = '';
                description.value = '';
                active.checked = true;
                title.textContent = 'Tambah Tipe Layanan';
                modalDescription.textContent = 'Masukkan nama dan deskripsi jenis layanan baru.';
                submit.textContent = 'Tambah Tipe Layanan';
            };

            const setEditForm = (trigger) => {
                form.action = trigger.dataset.typeAction;
                method.disabled = false;
                context.value = 'edit';
                typeId.value = trigger.dataset.typeId;
                name.value = trigger.dataset.typeName;
                description.value = trigger.dataset.typeDescription || '';
                active.checked = trigger.dataset.typeActive === '1';
                title.textContent = 'Edit Tipe Layanan';
                modalDescription.textContent = 'Perbarui nama, deskripsi, atau status tipe layanan.';
                submit.textContent = 'Simpan Perubahan';
            };

            document.querySelectorAll('[data-type-create-open]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    setCreateForm();
                    openModal();
                });
            });

            document.querySelectorAll('[data-type-edit-open]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    setEditForm(trigger);
                    openModal();
                });
            });

            modal.querySelectorAll('[data-type-modal-close]').forEach((element) => {
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
                    title.textContent = 'Edit Tipe Layanan';
                    modalDescription.textContent = 'Perbarui nama, deskripsi, atau status tipe layanan.';
                    submit.textContent = 'Simpan Perubahan';
                } else {
                    method.disabled = true;
                }

                openModal();
            }
        })();
    </script>
@endsection
