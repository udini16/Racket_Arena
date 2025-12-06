<?php

use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

Route::view('/login', 'login')->name('login');
Route::view('/customer-dashboard', 'customerDashboard');
Route::view('/admin-dashboard', 'adminDashboard');
Route::view('/employee-dashboard', 'employeeDashboard');



