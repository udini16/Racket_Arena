<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\PaymentController;

// 1. Homepage (The new file we just made)
Route::view('/', 'welcome');

Route::view('/login', 'login')->name('login');
Route::view('/customer-dashboard', 'customerDashboard');
Route::view('/admin-dashboard', 'adminDashboard');
Route::view('/employee-dashboard', 'employeeDashboard');
Route::get('/payment/mock-fpx/{id}', [PaymentController::class, 'showMockPage']);
Route::post('/payment/process/{id}', [PaymentController::class, 'processPayment']);

// 4. Logout Logic (CRITICAL: Keep this!)
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');