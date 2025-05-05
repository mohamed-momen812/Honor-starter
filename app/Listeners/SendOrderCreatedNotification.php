<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Modules\Notification\Services\NotificationService;

class SendOrderCreatedNotification
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(OrderCreated $event): void
    {
        $order = $event->order;

        $this->notificationService->createNotification([
            'user_id' => $order->user_id,
            'type' => 'order_created',
            'data' => [
                'order_id' => $order->id,
                'message' => "Your order #{$order->id} has been created and is {$order->status}.",
            ],
        ]);
    }
}