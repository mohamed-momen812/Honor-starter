<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('notifications', \Modules\Notification\Http\Controllers\NotificationController::class)
            ->middleware('permission:manage-notifications');
        Route::get('notifications', [\Modules\Notification\Http\Controllers\NotificationController::class, 'index'])
            ->middleware('permission:view-notifications')->withoutMiddleware('permission:manage-notifications');
    });
});
