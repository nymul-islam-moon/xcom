<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\Auth\AdminAuthenticatedSessionController;

// Public (guest:admin) admin auth routes
// Route::middleware('guest:admin')->group(function () {
//     Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
//     Route::post('login', [AdminAuthenticatedSessionController::class, 'store'])->name('login.store');
// });

// // Protected (auth:admin + ensure.admin set in bootstrap/app.php)
// Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('index');

// Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])
//     ->middleware('auth:admin')
//     ->name('logout');
Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('index');