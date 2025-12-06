<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourtController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/courts', [CourtController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Must have Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // --- 1. COMMON / CUSTOMER ---
    Route::post('/bookings', [BookingController::class, 'store']); // Book a court
    Route::get('/my-bookings', [BookingController::class, 'myBookings']); // See my history
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // --- 2. ADMIN ONLY ---
    Route::middleware('role:admin')->group(function () {
        Route::post('/courts', [CourtController::class, 'store']);
        Route::delete('/courts/{id}', [CourtController::class, 'destroy']);
        Route::get('/admin/stats', [DashboardController::class, 'stats']);
        
        // 👇 Add this so Admins can also see the booking list
        Route::get('/bookings', [BookingController::class, 'index']);
    });

    // --- 3. EMPLOYEE ONLY ---
    Route::middleware('role:employee')->group(function () {
        Route::put('/bookings/{id}/status', [BookingController::class, 'updateStatus']);
        Route::get('/employee/stats', [DashboardController::class, 'stats']);
        
        // 👇 This is required for the Staff Dashboard table
        Route::get('/bookings', [BookingController::class, 'index']);
    });
});