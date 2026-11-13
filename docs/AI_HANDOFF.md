
# AI Project Handoff

## Project
Laravel Transport Management System

## Current Status
- Authentication implemented.
- Login and registration pages available.
- Driver management page has full CRUD (admin only).
- Reservation create form converted to a 5-step wizard with review step.
- `/services` tables now sort by service date: active/upcoming nearest first, history most recent first.
- Project conventions recorded in `.ai/rules` via Laravel Boost `infer-conventions`.

## Technical Decisions
- Framework: Laravel 11 (PHP 8.2), Blade + Vite, PHPUnit installed.
- Follow existing authentication implementation.
- Preserve existing database architecture.
- Reuse existing UI components and styles.
- Keep reservation start/end times derived from itinerary stops instead of separate form inputs.
- Laravel Boost MCP is configured in `opencode.json` (`php artisan boost:mcp`).
- Project conventions live in `.ai/rules` and should be followed by future agents.

## Current Task
- Audit keamanan reservasi layanan: hanya pemilik dan hanya status Pending sebelum tanggal layanan yang dapat mengubah/membatalkan; admin hanya dapat approve/reject.

## Completed
- Added driver edit, update, and delete routes under the admin role group.
- Added `editDriver`, `updateDriver`, and `destroyDriver` to `TmsController`.
- Delete is blocked when the driver is still referenced by transport services.
- Driver list shows service usage count, Edit/Hapus actions, and a delete confirmation modal.
- Added `resources/views/drivers/edit.blade.php`.
- Rebuilt `resources/views/reservations/create.blade.php` as a 5-step wizard:
  - Service details, guest list, itinerary, notes, review/submit.
  - Client-side per-step validation, dynamic guest/stop rows with counters and remove actions.
  - Live review summary before submit; submit button shows loading state.
  - Supports restoring the initial step after validation errors.
- Reservations store service dates and itinerary stop times only; approval derives the transport service's `start_time`/`end_time` from the first/last itinerary stops.
- Vehicle plate numbers are stored on `transport_services`, entered during operational assignment or reservation approval, and copied from existing vehicles by the migration.
- New services and reservation approvals automatically snapshot the active Default emergency contact into the SPK; services and SPK display that contact without manual selection.
- Public Check In can optionally create an unscheduled, checked-in itinerary stop from `destination_name`, recorded as a single `check_in` SPK event; no duplicate `destination_added` event.
- Migration `2026_11_13_000001_remove_duplicate_destination_added_events` removes old `destination_added` events that duplicated a `check_in` event for the same stop.
- Added `itinerary_stops.*.note` validation (`nullable|string|max:500`) on store and update.
- Removed obsolete `start_time`/`end_time` request validation.
- Added 5-step wizard CSS (`service-wizard-steps--5`) and responsive breakpoints.
- Updated reservation cancel button to `service-reject-btn`.
- Sorted `/services` active/upcoming table by `service_date ASC`, then `start_time ASC`.
- Sorted `/services` history table by `service_date DESC`, then `start_time DESC`.
- Dashboard reservasi membuka detail reservasi layanan lewat modal (`resources/views/reservations/_detail-modal.blade.php`), termasuk daftar tamu, itinerary, dan penugasan layanan.
- Menghapus route, method `ReservationController::serviceDetail`, dan view `reservations/service-detail.blade.php` agar tidak ada dead code.
- `guest_count` reservasi tidak lagi ditimpa `count($guests)` pada store/update; nilai dari form "Jumlah Tamu" dipertahankan.
- Detail reservasi, review wizard, dan preview SPK menampilkan `guest_count` (bukan jumlah baris daftar tamu).
- Tambah/hapus tamu pada layanan tidak lagi menghitung ulang `guest_count` layanan.
- Menambahkan kolom/bagian "Jumlah Tamu" dari `reservations.guest_count` di dashboard admin, dashboard reservasi, daftar reservasi (pemohon & admin), daftar layanan, detail reservasi, detail layanan, preview SPK, dan SPK publik.
- `canEditReservation` sekarang hanya mengizinkan pemilik + status Pending + belum melewati tanggal layanan; admin tidak lagi dapat mengedit reservasi orang lain.
- `update`, `cancel`, dan `reject` memakai transaksi + `lockForUpdate` serta cek ulang status/ownership untuk mencegah race condition dan manipulasi langsung.
- Link Edit di daftar admin dihapus; link Edit di daftar pemohon dan halaman detail hanya tampil untuk pemilik + Pending.
- Admin tetap dapat approve/reject hanya selama status Pending.
- Recorded 12 project conventions as 15 path-scoped rules in `.ai/rules`:
  - Inline validation; business logic in controllers.
  - Role-based authorization; explicit named routes.
  - Raw SQL base schema plus incremental Schema migrations.
  - Statuses as plain strings.
  - Blade layout; inline page scripts; Indonesian user-facing text; DateFormatter.
  - Custom CSS via Vite; Asia/Makassar business dates.

## Pending
- Manual browser verification of the reservation dashboard detail modal (open, close, backdrop, Escape).
- Manual browser verification of `/services` ordering in both tables.
- Manual browser verification of the reservation wizard and driver CRUD flows.
- Commit the current working tree changes (not yet committed).
- Decide whether untracked `opencode.json`, `boost.json`, `.ai/`, and `docs/` should be committed.

## Important Files
- `.ai/rules/` (index plus app, controllers, css, http, migrations, routes, views)
- `routes/web.php`
- `app/Http/Controllers/TmsController.php`
- `app/Http/Controllers/ReservationController.php`
- `app/Models/Driver.php`
- `resources/views/services/index.blade.php`
- `resources/views/drivers/index.blade.php`
- `resources/views/drivers/edit.blade.php`
- `resources/views/dashboard-reservation.blade.php`
- `resources/views/reservations/_detail-modal.blade.php`
- `resources/views/reservations/create.blade.php`
- `resources/views/reservations/show.blade.php`
- `resources/css/app.css`

## Last Verification
- `php -l app/Http/Controllers/TmsController.php` — no syntax errors after the services sorting change.
- `php -l app/Http/Controllers/ReservationController.php` — no syntax errors.
- `php -l routes/web.php` — no syntax errors.
- `php artisan route:list --name=drivers` — 5 routes registered.
- `php artisan route:list --name=reservations` — 13 routes registered (service-detail removed).
- `php artisan view:cache` — Blade templates compiled successfully.
- `php -l app/Http/Controllers/ReservationController.php` dan `TmsController.php` — no syntax errors after the guest_count change.
- `php artisan view:cache` — Blade templates compiled successfully after adding guest count to all related views.
- Grep — no remaining `guests->count()` display in Blade views.
- Audit authorization — edit/update/cancel hanya untuk pemilik + Pending; approve/reject dibatasi Pending + lock; `php -l` dan `view:cache` OK.
- Laravel Boost query — 8 reservations, 0 with null/0 `guest_count`.
- `npm run build` — Vite production build succeeded.
- Laravel Boost `record-rule` — all 15 rule recordings succeeded.

## Next Action
- Uji manual: user lain membuka edit/update/cancel reservasi orang lain harus 403; edit reservasi Approved/Rejected/Cancelled harus 403; admin tidak melihat tombol Edit pada reservasi orang lain.
- Open the reservation dashboard and verify the detail modal for guest, itinerary, assignment, backdrop, Escape, and close button behavior.
- Open `/services` and confirm the active table is ordered nearest-to-farthest and the history table is ordered most-recent-past first.
- Manually verify `/reservations/create` across all 5 steps, including guest/itinerary rows and validation.
- Manually verify `/drivers`: create, edit, delete an unused driver, and confirm a referenced driver cannot be deleted.
