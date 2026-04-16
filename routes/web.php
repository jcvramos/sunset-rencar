<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Landing page pública
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Panel interno (requiere autenticación)
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Flota de vehículos
    Route::resource('vehicles', VehicleController::class)->except(['create', 'edit', 'show']);
    Route::patch('/vehicles/{vehicle}/status', [VehicleController::class, 'updateStatus'])->name('vehicles.status');

    // CRM — Clientes
    Route::resource('clients', ClientController::class)->except(['create', 'edit']);
    Route::patch('/clients/{client}/status', [ClientController::class, 'updateStatus'])->name('clients.status');

    // Calendario de disponibilidad
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/availability', [CalendarController::class, 'availability'])->name('calendar.availability');
    Route::get('/calendar/check', [CalendarController::class, 'check'])->name('calendar.check');
    Route::post('/calendar/block', [CalendarController::class, 'block'])->name('calendar.block');
    Route::post('/calendar/unblock', [CalendarController::class, 'unblock'])->name('calendar.unblock');

    // Políticas
    Route::get('/policies', [PolicyController::class, 'index'])->name('policies.index');
    Route::post('/policies/destinations', [PolicyController::class, 'storeDestination'])->name('policies.destinations.store');
    Route::patch('/policies/destinations/{destination}', [PolicyController::class, 'updateDestination'])->name('policies.destinations.update');
    Route::delete('/policies/destinations/{destination}', [PolicyController::class, 'destroyDestination'])->name('policies.destinations.destroy');
    Route::post('/policies/cancellations', [PolicyController::class, 'storeCancellation'])->name('policies.cancellations.store');
    Route::patch('/policies/cancellations/{cancellation}', [PolicyController::class, 'updateCancellation'])->name('policies.cancellations.update');
    Route::delete('/policies/cancellations/{cancellation}', [PolicyController::class, 'destroyCancellation'])->name('policies.cancellations.destroy');
});

require __DIR__.'/auth.php';
