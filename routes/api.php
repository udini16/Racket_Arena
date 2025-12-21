<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourtController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AdminCourtController;
use App\Http\Controllers\Api\AdminEmployeeController;
use App\Http\Controllers\Api\AdminRevenueController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/courts', [CourtController::class, 'index']); // Public list (read-only)
Route::get('/bookings/availability', [BookingController::class, 'checkAvailability']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Must have Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // 1. CUSTOMER ROUTES
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/bookings/{id}/pay', [PaymentController::class, 'checkout']);

    // 2. ADMIN ONLY ROUTES
    Route::middleware('role:admin')->group(function () {
        // 👇 Uses AdminCourtController for management actions
        Route::post('/courts', [AdminCourtController::class, 'store']);
        Route::delete('/courts/{id}', [AdminCourtController::class, 'destroy']);
        
        Route::get('/admin/stats', [DashboardController::class, 'stats']);
        Route::get('/bookings', [BookingController::class, 'index']);

        Route::post('/courts', [AdminCourtController::class, 'store']);
        Route::put('/courts/{id}', [AdminCourtController::class, 'update']); 
        Route::delete('/courts/{id}', [AdminCourtController::class, 'destroy']);

        Route::get('/admin/employees', [AdminEmployeeController::class, 'index']);
        Route::post('/admin/employees', [AdminEmployeeController::class, 'store']);
        Route::delete('/admin/employees/{id}', [AdminEmployeeController::class, 'destroy']);

        Route::get('/admin/revenue', [AdminRevenueController::class, 'index']);
    });

    // 3. EMPLOYEE ONLY ROUTES
    Route::middleware('role:employee')->group(function () {
        Route::put('/bookings/{id}/status', [BookingController::class, 'updateStatus']);
        Route::get('/employee/stats', [DashboardController::class, 'stats']);
        Route::get('/bookings', [BookingController::class, 'index']);
    });
});