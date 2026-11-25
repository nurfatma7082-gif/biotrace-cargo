<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ShipmentApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API Routes - BioTrace Platform
| Meets UAS Brief Slide 12 Requirements
|--------------------------------------------------------------------------
*/

// Public Authentication
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);

// Protected API Routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/user', [AuthApiController::class, 'user']);

    // Shipments
    Route::get('/shipments', [ShipmentApiController::class, 'index']);
    Route::post('/shipments', [ShipmentApiController::class, 'store']);
    Route::get('/shipments/{id}', [ShipmentApiController::class, 'show']);
    Route::match(['put', 'patch'], '/shipments/{id}', [ShipmentApiController::class, 'update']);
    Route::delete('/shipments/{id}', [ShipmentApiController::class, 'destroy']);

    // Tracking
    Route::get('/shipments/{id}/tracking', [ShipmentApiController::class, 'getTracking']);
    Route::post('/shipments/{id}/tracking', [ShipmentApiController::class, 'postTracking']);

    // Exceptions
    Route::get('/shipments/{id}/exceptions', [ShipmentApiController::class, 'getExceptions']);
    Route::post('/shipments/{id}/exceptions', [ShipmentApiController::class, 'postException']);
    Route::patch('/exceptions/{id}', [ShipmentApiController::class, 'patchException']);

    // Carriers & Locations
    Route::get('/carriers', [ShipmentApiController::class, 'listCarriers']);
    Route::post('/carriers', [ShipmentApiController::class, 'storeCarrier']);
    Route::get('/locations', [ShipmentApiController::class, 'listLocations']);
    Route::post('/locations', [ShipmentApiController::class, 'storeLocation']);
});
