<?php

use App\Http\Controllers\Admin\ProductBrandController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductChildCategoryController;
use App\Http\Controllers\Admin\ProductSubCategoryController;
use App\Http\Controllers\Backend\Shop\Auth\ShopAuthenticatedSessionController;
use App\Http\Controllers\Backend\Shop\ProductController;
use App\Http\Controllers\Backend\Shop\ShopDashboardController;
use Illuminate\Support\Facades\Route;


// Guest (shop) routes
Route::middleware('guest:shop')->group(function () {
    Route::get('login', [ShopAuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [ShopAuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth:shop')->group(function () {
    Route::post('logout', [ShopAuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [ShopDashboardController::class, 'index'])->name('dashboard');


    // Route::get('select-brands', [ProductBrandController::class, 'selectBrands'])
    //     ->name('select.brands');
    // Route::get('select-categories', [ProductCategoryController::class, 'selectCategories'])
    //     ->name('select.categories');
    // Route::get('select-sub-categories', [ProductSubCategoryController::class, 'selectSubCategories'])
    //     ->name('select.sub-categories'); 
    // Route::get('select-child-categories', [ProductChildCategoryController::class, 'selectChildCategories'])
    //     ->name('select.child-categories');
    Route::resource('/products', ProductController::class);
});
