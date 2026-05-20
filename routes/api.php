<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PropertyController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminReportController;

/*
|--------------------------------------------------------------------------|
| PUBLIC DATA
|--------------------------------------------------------------------------|
*/

Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
Route::post('/contact', [ContactController::class, 'send']);

/*
|--------------------------------------------------------------------------|
| PROTECTED (SANCTUM COOKIE AUTH)
|--------------------------------------------------------------------------|
*/

Route::middleware('auth:sanctum')->group(function () {

    // CURRENT USER
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // PROPERTIES (AUTH REQUIRED)
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::put('/properties/{id}', [PropertyController::class, 'update']);
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);

    // BOOKINGS (AUTH REQUIRED)
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);

    /*
    |--------------------------------------------------------------------------|
    | ADMIN ONLY
    |--------------------------------------------------------------------------|
    */

    Route::middleware('admin')->group(function () {
        Route::get('/admin/users', [AdminReportController::class, 'index']);
        Route::get('/admin/export-bookings', [AdminReportController::class, 'exportBookings']);
    });
});