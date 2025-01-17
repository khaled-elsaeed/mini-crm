<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CustomerController,
    EmployeeController,
    Admin\AdminDashboardController,
    Employee\EmployeeDashboardController,
    Auth\LoginController
};

// Apply throttling to all routes
Route::middleware('throttle:60,1')->group(function () {

    // Public Routes
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('home'); 

    // Authentication Routes
    Route::prefix('auth')->group(function () {
        // Route to show the login form
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        // Route to handle the login form submission
        Route::post('login', [LoginController::class, 'login']); 
        // Route to handle logout
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    });

    Route::resources([
        'employees' => EmployeeController::class, 
        'customers' => CustomerController::class, 
    ]);

    // Admin Routes (accessible by users with an admin role)
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        // Admin dashboard route
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });

    // Employee Routes (accessible by users with an employee role)
    Route::middleware(['auth'])->prefix('employee')->name('employee.')->group(function () {
        // Employee dashboard route
        Route::get('dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    });
});
