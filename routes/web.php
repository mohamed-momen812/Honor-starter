<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Models\Order;

Route::get('/', function () {
    $order = Order::find(1);
    $clientSecret = 'pi_3RLMQ6Q8Xa8kI8Hx0ZkPtSfx_secret_XJ1k37qKjnq0wEvCJdpc2IAxr';
    return view('welcome', compact('order', 'clientSecret'));
});