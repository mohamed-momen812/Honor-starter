<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('products', \Modules\Product\Http\Controllers\ProductController::class)
            ->middleware('permission:manage-products');
        Route::get('products', [\Modules\Product\Http\Controllers\ProductController::class, 'index'])
            ->middleware('permission:view-products')->withoutMiddleware('permission:manage-products');

        Route::apiResource('categories', \Modules\Product\Http\Controllers\CategoryController::class)
            ->middleware('permission:manage-categories');
        Route::get('categories', [\Modules\Product\Http\Controllers\CategoryController::class, 'index'])
            ->middleware('permission:view-categories')->withoutMiddleware('permission:manage-categories');
    });
});