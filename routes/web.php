
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TmsController;
use App\Http\Controllers\ReservationController;

Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Tautan publik SPK dengan format yang mudah dibagikan seperti /spk/{id}/{nomor-spk}.
Route::prefix('spk/{spk}/{spkNumber}')
    ->name('public.spk.number.')
    ->middleware('throttle:60,1')
    ->group(function () {
        Route::get('/', [TmsController::class, 'publicSpkByNumber'])->name('show');
        Route::post('/check-in', [TmsController::class, 'publicCheckInByNumber'])->name('checkin');
        Route::post('/destinations/{stop}/cancel', [TmsController::class, 'publicCancelDestinationByNumber'])->name('destinations.cancel');
    });

Route::prefix('public/spk')
    ->name('public.spk.')
    ->middleware('throttle:60,1')
    ->group(function () {
        Route::get('/{token}', [TmsController::class, 'publicSpk'])->name('show');
        Route::post('/{token}/check-in', [TmsController::class, 'publicCheckIn'])->name('checkin');
        Route::post('/{token}/destinations', [TmsController::class, 'publicAddDestination'])->name('destinations.add');
        Route::post('/{token}/destinations/{stop}/cancel', [TmsController::class, 'publicCancelDestination'])->name('destinations.cancel');
    });

Route::middleware(['auth'])->group(function () {
    Route::get('/', [TmsController::class, 'dashboard'])->name('dashboard');
    Route::middleware('role:admin,owner,manager')
        ->get('/dashboard/live-check-ins', [TmsController::class, 'liveDashboardCheckIns'])
        ->name('dashboard.live-check-ins');

    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Reservation: user dengan role reservation
    Route::middleware('role:reservation')->group(function () {
        Route::get('/reservations', [ReservationController::class, 'index'])
            ->name('reservations.index');
        Route::get('/reservations/create', [ReservationController::class, 'create'])
            ->name('reservations.create');
        Route::post('/reservations', [ReservationController::class, 'store'])
            ->name('reservations.store');
        Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])
            ->name('reservations.show');
        Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])
            ->name('reservations.cancel');
    });

    // Reservation: admin, owner, dan manager
    Route::middleware('role:admin,owner,manager')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/reservations', [ReservationController::class, 'adminIndex'])
                ->name('reservations.index');
            Route::get('/reservations/{reservation}/edit', [ReservationController::class, 'edit'])
                ->name('reservations.edit');
            Route::put('/reservations/{reservation}', [ReservationController::class, 'update'])
                ->name('reservations.update');
            Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])
                ->name('reservations.show');
            Route::post('/reservations/{reservation}/approve', [ReservationController::class, 'approve'])
                ->name('reservations.approve');
            Route::post('/reservations/{reservation}/reject', [ReservationController::class, 'reject'])
                ->name('reservations.reject');
        });

    // Semua user terautentikasi dapat melihat daftar dan detail layanan.
    Route::get('/services', [TmsController::class, 'services'])
        ->name('services.index');
    Route::get('/services/{service}/spk-preview', [TmsController::class, 'previewSpk'])
        ->whereNumber('service')
        ->name('services.spk.preview');
    Route::get('/services/{service}', [TmsController::class, 'showService'])
        ->whereNumber('service')
        ->name('services.show');

    // Admin dan manager dapat mengelola data operasional (kecuali manajemen reservasi).
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/services/create', [TmsController::class, 'createService'])
            ->name('services.create');
        Route::post('/services', [TmsController::class, 'storeService'])
            ->name('services.store');
        Route::post('/services/{service}/send-spk', [TmsController::class, 'sendSpkToDriver'])
            ->name('services.spk.send');
        Route::post('/services/{service}/send-info', [TmsController::class, 'sendServiceInfoToReservation'])
            ->name('services.info.send');

        Route::prefix('services/{service}/guests')
            ->name('services.guests.')
            ->group(function () {
                Route::post('/', [TmsController::class, 'storeGuest'])
                    ->name('store');
                Route::put('/{guest}', [TmsController::class, 'updateGuest'])
                    ->name('update');
                Route::delete('/{guest}', [TmsController::class, 'destroyGuest'])
                    ->name('destroy');
            });

        Route::prefix('services/{service}/stops')
            ->name('services.stops.')
            ->group(function () {
                Route::post('/', [TmsController::class, 'storeStop'])
                    ->name('store');
                Route::put('/{stop}', [TmsController::class, 'updateStop'])
                    ->name('update');
                Route::delete('/{stop}', [TmsController::class, 'destroyStop'])
                    ->name('destroy');
            });

        Route::get('/drivers', [TmsController::class, 'drivers'])
            ->name('drivers.index');
        Route::post('/drivers', [TmsController::class, 'storeDriver'])
            ->name('drivers.store');
        Route::put('/drivers/{driver}', [TmsController::class, 'updateDriver'])
            ->whereNumber('driver')
            ->name('drivers.update');

        Route::get('/vehicles', [TmsController::class, 'vehicles'])
            ->name('vehicles.index');
        Route::post('/vehicles', [TmsController::class, 'storeVehicle'])
            ->name('vehicles.store');
        Route::get('/vehicles/{vehicle}/edit', [TmsController::class, 'editVehicle'])
            ->whereNumber('vehicle')
            ->name('vehicles.edit');
        Route::put('/vehicles/{vehicle}', [TmsController::class, 'updateVehicle'])
            ->whereNumber('vehicle')
            ->name('vehicles.update');

        Route::get('/service-types', [TmsController::class, 'types'])
            ->name('types.index');
        Route::post('/service-types', [TmsController::class, 'storeType'])
            ->name('types.store');
        Route::put('/service-types/{type}', [TmsController::class, 'updateType'])
            ->whereNumber('type')
            ->name('types.update');

        Route::get('/emergency-contacts', [TmsController::class, 'contacts'])
            ->name('contacts.index');
        Route::post('/emergency-contacts', [TmsController::class, 'storeContact'])
            ->name('contacts.store');
        Route::put('/emergency-contacts/{contact}', [TmsController::class, 'updateContact'])
            ->whereNumber('contact')
            ->name('contacts.update');
        Route::delete('/emergency-contacts/{contact}', [TmsController::class, 'destroyContact'])
            ->whereNumber('contact')
            ->name('contacts.destroy');
    });

    // Manajemen user reservation: hanya admin.
    Route::middleware('role:admin')->group(function () {
        Route::get('/reservation-users', [TmsController::class, 'reservationUsers'])
            ->name('reservation-users.index');
        Route::post('/reservation-users', [TmsController::class, 'storeReservationUser'])
            ->name('reservation-users.store');
        Route::put('/reservation-users/{user}', [TmsController::class, 'updateReservationUser'])
            ->whereNumber('user')
            ->name('reservation-users.update');
    });
});
