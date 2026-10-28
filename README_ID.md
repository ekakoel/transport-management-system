# Bali Kami Tour — Transport Management System (MVP)

Paket awal aplikasi mandiri Laravel + MySQL, dirancang untuk hosting Hostinger dan halaman SPK driver yang ringan tanpa gambar, peta embed, atau asset frontend besar.

## Fitur yang sudah disertakan dalam source paket
- Login Admin/Reservation.
- Dashboard ringkas.
- Data driver, kendaraan, tipe layanan, kontak darurat.
- Buat layanan dan terbitkan SPK dengan token publik acak.
- Halaman SPK publik untuk browser HP tanpa login.
- Check-in GPS berdasarkan lokasi aktual; tidak ada geofence.
- Catatan check-in opsional.
- Tambah destinasi dan batalkan kunjungan dengan riwayat event.
- Waktu check-in dari server.
- SPK terkunci setelah tanggal layanan lewat, dibatalkan, atau selesai.
- Pelaporan event check-in pada detail layanan.
- Tampilan Blade/CSS tanpa gambar atau library JS eksternal.

## Penting sebelum produksi
Ini adalah MVP awal yang perlu diuji di staging sebelum dipakai untuk layanan sungguhan. Beberapa hal belum dibangun penuh: edit/delete data master, role-permission granular (saat ini login internal masih satu gate), maintenance UI, ekspor laporan, notifikasi WhatsApp, automated scheduler, dan audit log yang menyeluruh. Jangan menganggap fitur tersebut sudah selesai hanya karena tabel database tersedia.

Token SPK ditampilkan satu kali setelah SPK dibuat. Simpan tautannya sebelum berpindah halaman; token mentah tidak dapat dipulihkan dari hash database. Jika token hilang, fitur rotasi/reissue token administratif perlu ditambahkan.

## Persyaratan
- PHP 8.2+ dengan extension umum Laravel: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, Fileinfo, BCMath.
- MySQL 8.0+ atau versi MySQL Hostinger yang kompatibel.
- Composer untuk menginstal dependency Laravel 11.
- HTTPS aktif untuk GPS browser.

## Cara pemasangan yang disarankan

1. Buat database MySQL kosong dan user database di hPanel Hostinger.
2. Upload ZIP dan ekstrak ke folder aplikasi di luar `public_html` jika struktur hosting memungkinkan.
3. Import `database/schema_mysql.sql` melalui phpMyAdmin ke database kosong.
4. Salin `.env.example` menjadi `.env`, lalu isi koneksi database, `APP_URL`, `APP_KEY`, `TMS_ADMIN_EMAIL`, dan `TMS_ADMIN_PASSWORD`.
5. Di folder aplikasi, jalankan:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan db:seed --force
   ```
   Jika `composer` atau SSH tidak tersedia pada paket hosting, siapkan `vendor/` melalui lingkungan build lokal yang kompatibel lalu upload bersama source. Jangan upload file `.env` ke repository publik.
6. Pastikan document root domain diarahkan ke folder `public/` Laravel. Jangan membuat folder `app`, `vendor`, atau `.env` dapat diakses langsung dari web.
7. Pastikan `storage/` dan `bootstrap/cache/` dapat ditulis oleh PHP.
8. Ubah `APP_DEBUG=false`; periksa `APP_URL` memakai HTTPS.
9. Login menggunakan email/password yang disetel di `.env`.
10. Buat data driver, kendaraan, kontak darurat yang benar, dan tipe layanan sebelum membuat SPK.
11. Tes GPS dari HP sungguhan pada domain HTTPS, termasuk izin ditolak, GPS tidak tersedia, tanggal expired, serta double-submit.

## Membuat administrator
Di `.env`:
```dotenv
TMS_ADMIN_EMAIL=alamat-admin-anda
TMS_ADMIN_PASSWORD=gunakan-password-kuat-yang-unik
```
Lalu:
```bash
php artisan db:seed --force
```
Seeder membuat akun admin dari nilai tersebut. Ganti nilai contoh sebelum seeding produksi. Jangan membagikan password melalui chat atau memasukkannya ke Git.

## Catatan import SQL
`schema_mysql.sql` ditujukan untuk database kosong. SQL membuat tabel yang dibutuhkan source aplikasi dan tabel pendukung session/cache/queue. Jangan import ke database produksi yang sudah berisi tabel bernama sama.

## Aturan operasional
- Check-in memerlukan GPS dan menyimpan lokasi yang dilaporkan browser; tidak ada pembatasan jarak ke destinasi.
- Catatan opsional.
- Tanggal layanan ditentukan berdasarkan `Asia/Makassar`.
- Semua endpoint publik untuk perubahan memeriksa tanggal/status di server.
- Setelah tanggal akhir layanan terlewat, SPK tidak dapat diubah melalui tautan driver.
- Tidak ada pelacakan GPS terus-menerus.
- SPK ringan, tanpa gambar atau map embed.

## Pengujian sebelum produksi
- Buat SPK untuk tanggal hari ini dan pastikan token link hanya muncul saat pembuatan.
- Uji check-in dengan izin GPS diaktifkan dan ditolak.
- Uji note kosong dan note terisi.
- Uji tambah/batal destinasi.
- Uji layanan expired/cancelled/completed.
- Uji bahwa token SPK tidak dapat mengakses SPK lain.
- Uji konflik jadwal driver/kendaraan.
- Periksa error log Laravel dan backup database.
