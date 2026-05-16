<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\StripeWebhookController;

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminReportController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Auth routes (registration & login)
Route::prefix('auth')->group(function () {
    // Limit brute-force attempts
    Route::middleware('throttle:5,1')->post('/register', [AuthController::class, 'register']);
    Route::middleware('throttle:5,1')->post('/login', [AuthController::class, 'login']);
});

// Public property routes
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);

// Stripe webhook (public, no auth)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);


/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Current user info
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // CRUD for properties
    Route::prefix('properties')->group(function () {
        Route::post('/', [PropertyController::class, 'store']);
        Route::put('/{id}', [PropertyController::class, 'update']);
        Route::delete('/{id}', [PropertyController::class, 'destroy']);
    });

    // Bookings
    Route::prefix('bookings')->group(function () {
        Route::post('/', [BookingController::class, 'store']);
        Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->prefix('admin')->group(function () {
        // Manage users
        Route::get('/users', [AdminUserController::class, 'index']);

        // Export reports
        Route::get('/export-bookings', [AdminReportController::class, 'exportBookings']);
    });

});