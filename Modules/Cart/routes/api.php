<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('cart', [\Modules\Cart\Http\Controllers\CartController::class, 'index'])
            ->middleware('permission:view-cart|manage-cart');
        Route::post('cart', [\Modules\Cart\Http\Controllers\CartController::class, 'store'])
            ->middleware('permission:manage-cart|view-cart');
        Route::put('cart/items/{id}', [\Modules\Cart\Http\Controllers\CartController::class, 'update'])
            ->middleware('permission:manage-cart|view-cart');
        Route::delete('cart/items/{id}', [\Modules\Cart\Http\Controllers\CartController::class, 'destroy'])
            ->middleware('permission:manage-cart|view-cart');
        Route::post('cart/checkout', [\Modules\Cart\Http\Controllers\CartController::class, 'checkout'])
            ->middleware('permission:manage-cart|view-cart');
    });
});
