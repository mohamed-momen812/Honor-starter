<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('orders', \Modules\Order\Http\Controllers\OrderController::class)
            ->middleware('permission:manage-orders');
        Route::get('orders', [\Modules\Order\Http\Controllers\OrderController::class, 'index'])
            ->middleware('permission:view-orders')->withoutMiddleware('permission:manage-orders'); // thier is autherization in controller to show only user orders
    });
});