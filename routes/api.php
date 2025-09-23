<?php

use App\Http\Controllers\Api\SelectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/select-status', [SelectController::class, 'ActiveInactiveSelect'])->name('api.select-status');
Route::get('/select-categories', [SelectController::class, 'categorySelect'])->name('api.select-categories');
Route::get('/select-sub-categories/{categoryId}', [SelectController::class, 'subCategorySelect'])->name('api.select-sub-categories');
