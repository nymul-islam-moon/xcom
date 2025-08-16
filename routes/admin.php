<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\ProductCategoryController;

// Guest (admin) routes
Route::middleware('guest:admin')->group(function () {
    Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AdminAuthenticatedSessionController::class, 'store'])->name('login.store');
});

// Authenticated (admin) routes
Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('index');
    Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Product Routes
    Route::prefix('products')->as('products.')->group(function () {
        Route::resource('categories', ProductCategoryController::class);
    });

});
