<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Include API routes from each module directly
    require base_path('Modules/User/Routes/api.php');
    require base_path('Modules/Product/Routes/api.php');
    require base_path('Modules/Order/Routes/api.php');
    require base_path('Modules/Notification/Routes/api.php');
});
