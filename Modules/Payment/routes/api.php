<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('payments', [\Modules\Payment\Http\Controllers\PaymentController::class, 'index'])
            ->middleware('permission:view-payments');
        Route::post('payments', [\Modules\Payment\Http\Controllers\PaymentController::class, 'store'])
            ->middleware('permission:manage-payments|manage-cart');
        Route::get('payments/{id}', [\Modules\Payment\Http\Controllers\PaymentController::class, 'show'])
            ->middleware('permission:view-payments');
    });
});
