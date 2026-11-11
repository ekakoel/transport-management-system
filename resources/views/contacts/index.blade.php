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
        <div class="main-notice mb-3">
            <span class="main-notice-icon">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M12 11v5m0-8h.01" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round"/>
                </svg>
            </span>
            <div>
                <strong>Pastikan kontak dapat dihubungi</strong>
                <p>Gunakan nomor telepon dan WhatsApp yang aktif. Kontak aktif yang ditandai sebagai Default akan dicantumkan otomatis pada layanan dan SPK.</p>
            </div>
        </div>
</header>
    <div class="service-create-page">
        <div class="tms-two-column">
            <div class="tms-main-column">
        

        {{-- Daftar Kontak --}}
        <section class="service-create-card">
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
                <button type="button" class="tms-modal-btn tms-modal-btn-primary"
                    data-contact-create-open>Tambah Kontak</button>
            </div>

            <div class="services-table-scroll">
                <table class="services-table">
                    <thead>
                        <tr>
                            <th>Nama Tim / Petugas</th>
                            <th>Telepon</th>
                            <th>WhatsApp</th>
                            <th>Default</th>
                            <th>Status</th>
                            <th>Aksi</th>
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
                                <td>
                                    <button type="button" class="services-detail-btn"
                                        data-contact-edit-open
                                        data-contact-action="{{ route('contacts.update', $c) }}"
                                        data-contact-id="{{ $c->id }}"
                                        data-contact-name="{{ $c->name }}"
                                        data-contact-phone="{{ $c->phone }}"
                                        data-contact-whatsapp="{{ $c->whatsapp }}"
                                        data-contact-default="{{ $c->is_default ? '1' : '0' }}"
                                        data-contact-active="{{ $c->is_active ? '1' : '0' }}">Edit</button>
                                    <button type="button" class="services-detail-btn"
                                        data-contact-delete-open
                                        data-contact-action="{{ route('contacts.destroy', $c) }}"
                                        data-contact-name="{{ $c->name }}">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 28px;">
                                    Belum ada kontak terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        @php
            $editingContact = $contacts->firstWhere('id', old('contact_id'));
            $contactModalContext = old('contact_context', 'create');
        @endphp
        <div class="tms-modal" id="contactModal" hidden
            data-open="{{ $errors->any() ? '1' : '0' }}"
            data-context="{{ $contactModalContext }}"
            data-contact-id="{{ old('contact_id') }}"
            data-edit-action="{{ $editingContact ? route('contacts.update', $editingContact) : '' }}">
            <div class="tms-modal-backdrop" data-contact-modal-close></div>
            <div class="tms-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="contactModalTitle">
                <div class="tms-modal-header">
                    <div>
                        <h2 id="contactModalTitle">Tambah Kontak Darurat</h2>
                        <p id="contactModalDescription">Masukkan informasi tim atau petugas operasional.</p>
                    </div>
                    <button type="button" class="tms-modal-close" data-contact-modal-close
                        aria-label="Tutup">&times;</button>
                </div>
                <form id="contactForm" method="POST" action="{{ route('contacts.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="contactMethod" value="PUT" disabled>
                    <input type="hidden" name="contact_context" id="contactContext" value="{{ $contactModalContext }}">
                    <input type="hidden" name="contact_id" id="contactId" value="{{ old('contact_id') }}">
                    <div class="tms-modal-body">
                        <div class="service-create-field">
                            <label for="contactName">Nama Tim / Petugas <span>*</span></label>
                            <input id="contactName" type="text" name="name" maxlength="150"
                                value="{{ old('name') }}" placeholder="Contoh: Operasional Bali Kami Tour" required>
                            @error('name')
                                <small class="service-create-error">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="service-create-field">
                            <label for="contactPhone">Nomor Telepon <span>*</span></label>
                            <input id="contactPhone" type="tel" name="phone" maxlength="40"
                                value="{{ old('phone') }}" placeholder="Contoh: +62 812..." autocomplete="tel" required>
                            @error('phone')
                                <small class="service-create-error">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="service-create-field">
                            <label for="contactWhatsapp">Nomor WhatsApp</label>
                            <input id="contactWhatsapp" type="tel" name="whatsapp" maxlength="40"
                                value="{{ old('whatsapp') }}" placeholder="Contoh: 628123456789">
                            @error('whatsapp')
                                <small class="service-create-error">{{ $message }}</small>
                            @enderror
                            <small class="service-create-help">Masukkan nomor WhatsApp dengan kode negara jika tersedia.</small>
                        </div>
                        <label style="display: inline-flex; align-items: flex-start; gap: 10px; cursor: pointer; margin: 0;">
                            <input type="hidden" name="is_default" value="0">
                            <input id="contactDefault" type="checkbox" name="is_default" value="1"
                                style="width: auto; margin: 3px 0 0;"
                                @checked(old('is_default', '0') == '1')>
                            <span>
                                <strong>Jadikan Kontak Default</strong>
                                <small style="display: block;">Kontak Default aktif dicantumkan otomatis pada layanan dan SPK.</small>
                            </span>
                        </label>
                        <label style="display: inline-flex; align-items: flex-start; gap: 10px; cursor: pointer; margin: 0;">
                            <input type="hidden" name="is_active" value="0">
                            <input id="contactActive" type="checkbox" name="is_active" value="1"
                                style="width: auto; margin: 3px 0 0;"
                                @checked(old('is_active', '1') == '1')>
                            <span>
                                <strong>Kontak Aktif</strong>
                                <small style="display: block;">Tandai jika kontak dapat digunakan untuk keperluan operasional.</small>
                            </span>
                        </label>
                    </div>
                    <div class="tms-modal-footer">
                        <button type="button" class="tms-modal-btn tms-modal-btn-secondary"
                            data-contact-modal-close>Batal</button>
                        <button type="submit" class="tms-modal-btn tms-modal-btn-primary" id="contactSubmit">
                            Tambah Kontak
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="tms-modal" id="contactDeleteModal" hidden>
            <div class="tms-modal-backdrop" data-contact-delete-close></div>
            <div class="tms-modal-dialog tms-modal-dialog-sm" role="dialog" aria-modal="true"
                aria-labelledby="contactDeleteTitle">
                <div class="tms-modal-header">
                    <h2 id="contactDeleteTitle">Hapus Kontak</h2>
                    <button type="button" class="tms-modal-close" data-contact-delete-close
                        aria-label="Tutup">&times;</button>
                </div>
                <form id="contactDeleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="tms-modal-body">
                        <p>Hapus kontak <strong id="contactDeleteName"></strong> dari sistem?</p>
                    </div>
                    <div class="tms-modal-footer">
                        <button type="button" class="tms-modal-btn tms-modal-btn-secondary"
                            data-contact-delete-close>Batal</button>
                        <button type="submit" class="tms-modal-btn tms-modal-btn-danger">Hapus Kontak</button>
                    </div>
                </form>
            </div>
        </div>
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
                <div class="tms-info-panel-footer">
                    <button type="button" class="tms-modal-btn tms-modal-btn-primary"
                        data-contact-create-open>Tambah kontak</button>
                </div>
            </aside>
        </div>
    </div>
    <script>
        (() => {
            const contactModal = document.getElementById('contactModal');
            const deleteModal = document.getElementById('contactDeleteModal');
            const contactForm = document.getElementById('contactForm');
            const deleteForm = document.getElementById('contactDeleteForm');

            if (!contactModal || !deleteModal || !contactForm || !deleteForm) return;

            const contactMethod = document.getElementById('contactMethod');
            const contactContext = document.getElementById('contactContext');
            const contactId = document.getElementById('contactId');
            const contactName = document.getElementById('contactName');
            const contactPhone = document.getElementById('contactPhone');
            const contactWhatsapp = document.getElementById('contactWhatsapp');
            const contactDefault = document.getElementById('contactDefault');
            const contactActive = document.getElementById('contactActive');
            const contactTitle = document.getElementById('contactModalTitle');
            const contactDescription = document.getElementById('contactModalDescription');
            const contactSubmit = document.getElementById('contactSubmit');
            const deleteName = document.getElementById('contactDeleteName');

            const syncBodyLock = () => {
                document.body.classList.toggle(
                    'tms-modal-open',
                    !contactModal.hidden || !deleteModal.hidden
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

            const setCreateForm = () => {
                contactForm.reset();
                contactForm.action = @json(route('contacts.store'));
                contactMethod.disabled = true;
                contactContext.value = 'create';
                contactId.value = '';
                contactName.value = '';
                contactPhone.value = '';
                contactWhatsapp.value = '';
                contactDefault.checked = false;
                contactActive.checked = true;
                contactTitle.textContent = 'Tambah Kontak Darurat';
                contactDescription.textContent = 'Masukkan informasi tim atau petugas operasional.';
                contactSubmit.textContent = 'Tambah Kontak';
            };

            const setEditForm = (trigger) => {
                contactForm.action = trigger.dataset.contactAction;
                contactMethod.disabled = false;
                contactContext.value = 'edit';
                contactId.value = trigger.dataset.contactId;
                contactName.value = trigger.dataset.contactName;
                contactPhone.value = trigger.dataset.contactPhone;
                contactWhatsapp.value = trigger.dataset.contactWhatsapp || '';
                contactDefault.checked = trigger.dataset.contactDefault === '1';
                contactActive.checked = trigger.dataset.contactActive === '1';
                contactTitle.textContent = 'Edit Kontak Darurat';
                contactDescription.textContent = 'Perbarui informasi kontak yang sudah terdaftar.';
                contactSubmit.textContent = 'Simpan Perubahan';
            };

            document.querySelectorAll('[data-contact-create-open]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    setCreateForm();
                    openModal(contactModal, contactName);
                });
            });

            document.querySelectorAll('[data-contact-edit-open]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    setEditForm(trigger);
                    openModal(contactModal, contactName);
                });
            });

            document.querySelectorAll('[data-contact-delete-open]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    deleteForm.action = trigger.dataset.contactAction;
                    deleteName.textContent = trigger.dataset.contactName;
                    openModal(deleteModal, deleteModal.querySelector('.tms-modal-close'));
                });
            });

            contactModal.querySelectorAll('[data-contact-modal-close]').forEach((element) => {
                element.addEventListener('click', () => closeModal(contactModal));
            });

            deleteModal.querySelectorAll('[data-contact-delete-close]').forEach((element) => {
                element.addEventListener('click', () => closeModal(deleteModal));
            });

            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') return;
                if (!deleteModal.hidden) closeModal(deleteModal);
                if (!contactModal.hidden) closeModal(contactModal);
            });

            if (contactModal.dataset.open === '1') {
                const isEdit = contactModal.dataset.context === 'edit'
                    && contactModal.dataset.editAction;

                if (isEdit) {
                    contactForm.action = contactModal.dataset.editAction;
                    contactMethod.disabled = false;
                    contactTitle.textContent = 'Edit Kontak Darurat';
                    contactDescription.textContent = 'Perbarui informasi kontak yang sudah terdaftar.';
                    contactSubmit.textContent = 'Simpan Perubahan';
                } else {
                    contactMethod.disabled = true;
                }

                openModal(contactModal, contactName);
            }
        })();
    </script>
@endsection
