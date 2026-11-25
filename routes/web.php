<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExceptionController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TrackingEventController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - BioTrace Platform
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick-login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Shipments
    Route::resource('shipments', ShipmentController::class)->except(['edit', 'update']);
    Route::post('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])->name('shipments.status');
    Route::post('/shipments/{shipment}/eta', [ShipmentController::class, 'updateEta'])->name('shipments.eta');
    Route::post('/shipments/{shipment}/tracking', [TrackingEventController::class, 'store'])->name('shipments.tracking.store');
    Route::post('/shipments/{shipment}/exceptions', [ExceptionController::class, 'store'])->name('shipments.exceptions.store');

    // Exceptions
    Route::get('/exceptions', [ExceptionController::class, 'index'])->name('exceptions.index');
    Route::patch('/exceptions/{exception}', [ExceptionController::class, 'update'])->name('exceptions.update');

    // Master Data
    Route::get('/carriers', [CarrierController::class, 'index'])->name('carriers.index');
    Route::post('/carriers', [CarrierController::class, 'store'])->name('carriers.store');
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
});
