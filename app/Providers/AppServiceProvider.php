<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;
use App\Listeners\OrderStatusUpdatedListener;
use App\Listeners\SendOrderCreatedNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            OrderStatusUpdated::class,
            OrderStatusUpdatedListener::class,
        );
        Event::listen(
            OrderCreated::class,
            SendOrderCreatedNotification::class,
        );
    }
}
