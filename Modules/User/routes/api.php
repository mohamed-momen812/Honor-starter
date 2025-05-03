<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/login', [\Modules\User\Http\Controllers\AuthController::class, 'login']);
    Route::post('/logout', [\Modules\User\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');

    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('users', \Modules\User\Http\Controllers\UserController::class)
            ->middleware('permission:manage-users');
        Route::get('roles', [\Modules\User\Http\Controllers\RoleController::class, 'index'])
            ->middleware('permission:manage-users');
    });
});
