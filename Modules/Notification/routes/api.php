<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/notifications', [\Modules\Notification\Http\Controllers\NotificationController::class, 'index'])
            ->middleware('permission:view-notifications');
    });
});
