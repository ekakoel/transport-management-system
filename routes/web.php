<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TmsController;

Route::get('/login',[AuthController::class,'show'])->name('login');
Route::post('/login',[AuthController::class,'login'])->middleware('throttle:10,1')->name('login.submit');
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');

Route::prefix('public/spk')->name('public.spk.')->middleware('throttle:60,1')->group(function(){
    Route::get('/{token}',[TmsController::class,'publicSpk'])->name('show');
    Route::post('/{token}/check-in',[TmsController::class,'publicCheckIn'])->name('checkin');
    Route::post('/{token}/destinations',[TmsController::class,'publicAddDestination'])->name('destinations.add');
    Route::post('/{token}/destinations/{stop}/cancel',[TmsController::class,'publicCancelDestination'])->name('destinations.cancel');
    Route::post('/{token}/complete',[TmsController::class,'publicComplete'])->name('complete');
});

Route::middleware(['auth'])->group(function(){
    Route::get('/',[TmsController::class,'dashboard'])->name('dashboard');
    Route::get('/services',[TmsController::class,'services'])->name('services.index');
    Route::get('/services/create',[TmsController::class,'createService'])->name('services.create');
    Route::post('/services',[TmsController::class,'storeService'])->name('services.store');
    Route::get('/services/{service}/spk-preview',[TmsController::class,'previewSpk'])->name('services.spk.preview');
    Route::get('/services/{service}',[TmsController::class,'showService'])->name('services.show');
    Route::get('/drivers',[TmsController::class,'drivers'])->name('drivers.index');
    Route::post('/drivers',[TmsController::class,'storeDriver'])->name('drivers.store');
    Route::get('/vehicles',[TmsController::class,'vehicles'])->name('vehicles.index');
    Route::post('/vehicles',[TmsController::class,'storeVehicle'])->name('vehicles.store');
    Route::get('/service-types',[TmsController::class,'types'])->name('types.index');
    Route::post('/service-types',[TmsController::class,'storeType'])->name('types.store');
    Route::get('/emergency-contacts',[TmsController::class,'contacts'])->name('contacts.index');
    Route::post('/emergency-contacts',[TmsController::class,'storeContact'])->name('contacts.store');
});
