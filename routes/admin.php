<?php

use App\Http\Controllers\Backend\Admin\AdminController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Backend\Auth\AdminAuthenticatedSessionController;

use App\Http\Controllers\Backend\Admin\ProductCategoryController;
use App\Http\Controllers\Backend\Admin\ProductChildCategoryController;
use App\Http\Controllers\Backend\Admin\ProductSubCategoryController;
use App\Http\Controllers\Backend\DashboardController;

// Guest (admin) routes
Route::middleware('guest:admin')->group(function () {
    Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AdminAuthenticatedSessionController::class, 'store'])->name('login.store');
});

// Authenticated (admin) routes
Route::middleware('auth:admin')->group(function () {
    
    Route::get('/phpinfo', fn() => phpinfo());

    // auth route
    Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // one-click verify (no login required), signed + throttled
    Route::get('/email/verify/{id}/{hash}', [AdminAuthenticatedSessionController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Product Routes
    Route::prefix('products')->as('products.')->group(function () {

        Route::resource('categories', ProductCategoryController::class);
        Route::resource('sub-categories', ProductSubCategoryController::class);
        Route::resource('child-categories', ProductChildCategoryController::class);

        // Route::resource('brands', ProductBrandController::class);

        // Route::resource('attributes', ProductAttributeController::class);

        // Route::prefix('attributes/{attribute}')->group(function () {
        //     Route::get('values/create', [ProductAttributeValueController::class, 'create'])->name('attribute-values.create');
        // });
        // Route::post('attribute-values/store', [ProductAttributeValueController::class, 'store'])->name('attribute-values.store');
        // Route::get('attribute-values/{attributeValue}/show', [ProductAttributeValueController::class, 'show'])->name('attribute-values.show');
        // Route::get('attribute-values/{attributeValue}/edit', [ProductAttributeValueController::class, 'edit'])->name('attribute-values.edit');
        // Route::put('attribute-values/{attributeValue}', [ProductAttributeValueController::class, 'update'])->name('attribute-values.update');
        // Route::delete('attribute-values/{attributeValue}', [ProductAttributeValueController::class, 'destroy'])->name('attribute-values.destroy');
    });

    // Shop Management Routes
    // Route::resource('shops', ShopController::class);
    // User Management Routes
    Route::resource('users', AdminController::class);


    // Bulk Upload
    Route::prefix('bulk-upload')->as('bulkUpload.')->group(function () {
        // Route::get('shops', [ShopController::class, 'show_bulk_upload'])->name('shop');
        // Route::post('shops/store', [ShopController::class, 'bulkUpload'])->name('shop.store');
    });
});
