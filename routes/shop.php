<?php

use App\Http\Controllers\Backend\Shop\Auth\ShopAuthenticatedSessionController;
use App\Http\Controllers\Backend\Shop\ProductController;
use App\Http\Controllers\Backend\Shop\ShopDashboardController;
use App\Http\Controllers\Backend\Shop\VerificationController;
use Illuminate\Support\Facades\Route;

// Guest (shop) routes
Route::middleware('guest:shop')->group(function () {
    Route::get('login', [ShopAuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [ShopAuthenticatedSessionController::class, 'store'])->name('login.store');
});
Route::get('email/verify', [VerificationController::class, 'verify'])
        ->name('verification.verify')
        ->middleware('signed');
Route::middleware('auth:shop')->group(function () {
    Route::post('logout', [ShopAuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [ShopDashboardController::class, 'index'])->name('dashboard');

    Route::resource('/products', ProductController::class);
});
