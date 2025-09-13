<?php

use App\Http\Controllers\Shop\ProductCategoryController;
use App\Http\Controllers\Shop\Auth\ShopAuthenticatedSessionController;
use App\Http\Controllers\Shop\ShopDashboardController;
use Illuminate\Support\Facades\Route;


// Guest (shop) routes
Route::middleware('guest:shop')->group(function () {
    Route::get('login', [ShopAuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [ShopAuthenticatedSessionController::class, 'store'])->name('login.store');
});

// Authenticated (shop) routes
Route::middleware('auth:shop')->group(function () {
    Route::post('logout', [ShopAuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [ShopDashboardController::class, 'index'])->name('dashboard');


    Route::prefix('products')->as('products.')->group(function () {
       
    });
});
