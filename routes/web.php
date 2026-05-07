<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CancellationController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DamageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReservationExtensionController;
use App\Http\Controllers\ReservationPhotoController;
use App\Http\Controllers\SublessorController;
use App\Http\Controllers\SublessorPayableController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehiclePhotoController;
use Illuminate\Support\Facades\Route;

// Landing page pública
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Panel interno (requiere autenticación)
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // === FASE 1 ===
    Route::resource('vehicles', VehicleController::class)->except(['create', 'edit', 'show']);
    Route::patch('/vehicles/{vehicle}/status', [VehicleController::class, 'updateStatus'])->name('vehicles.status');
    Route::post('/vehicles/{vehicle}/photos', [VehiclePhotoController::class, 'store'])->name('vehicles.photos.store');
    Route::delete('/vehicles/{vehicle}/photos/{zone}', [VehiclePhotoController::class, 'destroy'])->name('vehicles.photos.destroy');
    Route::patch('/vehicles/{vehicle}/photos/{zone}/main', [VehiclePhotoController::class, 'setMain'])->name('vehicles.photos.main');

    Route::resource('clients', ClientController::class)->except(['create', 'edit']);
    Route::patch('/clients/{client}/status', [ClientController::class, 'updateStatus'])->name('clients.status');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/availability', [CalendarController::class, 'availability'])->name('calendar.availability');
    Route::get('/calendar/check', [CalendarController::class, 'check'])->name('calendar.check');
    Route::post('/calendar/block', [CalendarController::class, 'block'])->name('calendar.block');
    Route::post('/calendar/unblock', [CalendarController::class, 'unblock'])->name('calendar.unblock');

    Route::get('/policies', [PolicyController::class, 'index'])->name('policies.index');
    Route::post('/policies/destinations', [PolicyController::class, 'storeDestination'])->name('policies.destinations.store');
    Route::patch('/policies/destinations/{destination}', [PolicyController::class, 'updateDestination'])->name('policies.destinations.update');
    Route::delete('/policies/destinations/{destination}', [PolicyController::class, 'destroyDestination'])->name('policies.destinations.destroy');
    Route::post('/policies/cancellations', [PolicyController::class, 'storeCancellation'])->name('policies.cancellations.store');
    Route::patch('/policies/cancellations/{cancellation}', [PolicyController::class, 'updateCancellation'])->name('policies.cancellations.update');
    Route::delete('/policies/cancellations/{cancellation}', [PolicyController::class, 'destroyCancellation'])->name('policies.cancellations.destroy');

    // === FASE 2 — Core del Negocio ===

    // Reservas (flujo de 9 etapas)
    Route::resource('reservations', ReservationController::class)->except(['edit']);
    Route::post('/reservations/{reservation}/advance', [ReservationController::class, 'advance'])->name('reservations.advance');
    Route::post('/reservations/{reservation}/deposit', [ReservationController::class, 'payDeposit'])->name('reservations.deposit');

    // Fotos guiadas (8 zonas)
    Route::post('/reservations/{reservation}/photos', [ReservationPhotoController::class, 'store'])->name('reservations.photos.store');
    Route::delete('/reservations/{reservation}/photos/{photo}', [ReservationPhotoController::class, 'destroy'])->name('reservations.photos.destroy');

    // Extensiones
    Route::post('/reservations/{reservation}/extensions', [ReservationExtensionController::class, 'store'])->name('reservations.extensions.store');

    // Cancelaciones
    Route::get('/cancellations', [CancellationController::class, 'index'])->name('cancellations.index');
    Route::post('/reservations/{reservation}/cancellation', [CancellationController::class, 'store'])->name('reservations.cancellation.store');
    Route::post('/cancellations/{cancellation}/sign', [CancellationController::class, 'sign'])->name('cancellations.sign');

    // Subarrendadores
    Route::resource('sublessors', SublessorController::class)->except(['create', 'edit', 'show']);

    // Cuentas por pagar a subarrendadores
    Route::get('/sublessors-payables', [SublessorPayableController::class, 'index'])->name('sublessors.payables.index');
    Route::post('/sublessors-payables/{payable}/pay', [SublessorPayableController::class, 'pay'])->name('sublessors.payables.pay');
    Route::patch('/sublessors-payables/{payable}', [SublessorPayableController::class, 'update'])->name('sublessors.payables.update');

    // Daños
    Route::get('/damages', [DamageController::class, 'index'])->name('damages.index');
    Route::post('/damages', [DamageController::class, 'store'])->name('damages.store');
    Route::post('/damages/{damage}/photos', [DamageController::class, 'addPhotos'])->name('damages.photos.add');
    Route::delete('/damages/{damage}/photos', [DamageController::class, 'removePhoto'])->name('damages.photos.remove');
    Route::patch('/damages/{damage}', [DamageController::class, 'update'])->name('damages.update');
    Route::delete('/damages/{damage}', [DamageController::class, 'destroy'])->name('damages.destroy');
});

require __DIR__.'/auth.php';
