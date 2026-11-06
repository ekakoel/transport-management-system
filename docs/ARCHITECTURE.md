# Arsitektur & Tema Project

## Stack

- Laravel dengan Blade sebagai template engine.
- Asset frontend dibundel menggunakan Vite melalui `@vite(['resources/css/app.css', 'resources/js/app.js'])` di `resources/views/layouts/app.blade.php`.
- Tidak menggunakan framework CSS eksternal. Seluruh tema aplikasi berada di `resources/css/app.css`.

## Sumber Tema

`resources/css/app.css` adalah sumber utama (source of truth) untuk warna, tipografi, layout, dan komponen UI.

- Tema aplikasi terautentikasi berada pada bagian `BALI KAMI TMS — MODERN DASHBOARD`.
- Halaman login dan beberapa elemen publik memiliki aksen tambahan yang dideklarasikan pada blok `:root` pertama.
- Blok `:root` kedua mendefinisikan ulang sebagian token inti. Untuk halaman aplikasi, gunakan nilai token dari blok dashboard.

## Palet Warna

| Token | Nilai | Penggunaan |
| --- | --- | --- |
| `--tms-navy` | `#101a32` | Sidebar, judul, elemen bernuansa gelap |
| `--tms-blue` | `#4263eb` | Aksi utama, link, fokus input |
| `--tms-muted` | `#8490a5` | Teks sekunder dan keterangan |
| `--tms-border` | `#e9edf4` | Border kartu, pemisah, dan input |
| `--tms-surface` | `#ffffff` | Permukaan kartu dan panel |
| `--tms-background` | `#f5f7fb` | Latar halaman aplikasi |
| `--tms-danger` | `#d93850` | Aksen bahaya/peringatan |
| `--tms-cyan` | `#24c9c3` | Aksen khusus login dan halaman publik SPK |
| `--tms-orange` | `#ff9e58` | Aksen khusus login dan halaman publik SPK |
| `--tms-text` | `#102338` | Teks utama pada tema login |
| `--tms-panel` | `#ffffff` | Panel pada tema login |

Warna netral yang umum digunakan: teks utama `#202b42` / `#1d2942`, teks sekunder `#718096` / `#8994a8`, dan border kartu `#e9edf5`.

## Tipografi

- Font diimpor dari Google Fonts: `DM Sans` dan `Manrope`.
- `Manrope` digunakan untuk judul panel, heading utama, dan nilai statistik.
- `DM Sans` digunakan pada halaman login dan halaman publik.
- Body halaman dashboard menggunakan stack `Inter, "Segoe UI", Arial, sans-serif`.
- Form dan tombol menggunakan `font-family: inherit` agar mengikuti konteks halaman.
- Ukuran teks utama aplikasi relatif kecil: judul `clamp(24px, 2.2vw, 31px)`, isi `11px–13px`, label `10px–11px`.

## Layout dan Permukaan

- Latar aplikasi: `--tms-background` (`#f5f7fb`).
- Sidebar: gradien `#111c35` ke `#0d162b`.
- Kartu/panel: latar putih, border `1px` bernuansa `#e9edf5`, radius `8px–16px`, dan shadow halus `rgb(26 42 75 / 3%)`.
- Grid dua kolom: `.tms-two-column` dengan rasio `7fr : 3fr`, bertumpuk pada layar `<= 1100px`.
- Komponen layout utama:
  - `.service-create-page` untuk halaman formulir.
  - `.service-create-card` untuk kartu formulir.
  - `.tms-info-panel` untuk panel informasi samping.
  - `.main-header` untuk header halaman.

## Tombol

| Peran | Kelas | Karakter Visual |
| --- | --- | --- |
| Aksi utama | `.service-submit-btn`, `.services-create-btn`, `.dashboard-create-btn` | Biru `#4263eb`, teks putih, radius `8px–10px`, tinggi minimal `43px–44px`, hover `#3452d1` |
| Sekunder / batal navigasi | `.service-cancel-btn` | Latar putih, border `#e1e6ef`, teks `#68758c`, hover `#f7f9fc` |
| Aksi destruktif pada form | `.service-reject-btn` | Merah solid `#ff2b2b`, border `#ef0a0a`, teks putih, hover `#c22323` |
| Aksi destruktif pada modal | `.tms-modal-btn-danger` | Merah `#c53b4a`, teks putih, hover `#a92a39` |
| Aksi kecil di tabel | `.services-detail-btn` | Biru `#4263eb`, latar `#f5f7ff`, border `#e2e8ff` |

Aturan penggunaan:

1. Gunakan kelas yang sudah ada; jangan membuat warna atau style tombol baru tanpa kebutuhan yang jelas.
2. Aksi utama harus memakai varian primer, bukan warna acak atau style bawaan browser.
3. Aksi destruktif wajib memakai varian danger (`.service-reject-btn` atau `.tms-modal-btn-danger`).
4. Kelas lama seperti `.resv-btn red` tidak memiliki definisi CSS dan tidak boleh digunakan kembali.
5. Tombol dalam form mengikuti pola `.service-create-actions` agar perataan dan jarak konsisten.

## Form dan Validasi

- Bungkus setiap field dengan `.service-create-field`.
- Input, select, dan textarea memakai border `#e1e6ef`, radius `8px`, tinggi minimal `42px`, dan fokus biru `#7890f5` dengan ring `rgb(66 99 235 / 9%)`.
- Pesan error menggunakan `.service-create-error`; teks bantuan menggunakan `.service-create-help`.
- Field lebar penuh menggunakan `.service-field-full`.

## Status dan Notifikasi

- Badge status reservasi memakai `.resv-status` dengan varian `Pending`, `Approved`, `Rejected`, dan `Cancelled`.
- Notifikasi global memakai `.alert` (sukses) dan `.alert.error` / `.alert--error` (gagal).
- Banner informasi halaman memakai `.main-notice`.

## Prinsip Implementasi Tema

1. Periksa `resources/css/app.css` sebelum menambah style baru.
2. Prioritaskan reuse kelas komponen yang sudah ada.
3. Hindari inline style untuk warna, border, atau tipografi.
4. Jaga konsistensi radius, shadow, dan palet dengan komponen sekitar.
5. Jika sebuah komponen belum memiliki style, tambahkan aturan baru di `app.css` menggunakan token `:root`, bukan nilai warna terpisah.
