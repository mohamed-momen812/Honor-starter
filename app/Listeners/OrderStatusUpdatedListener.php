<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use Modules\Notification\Services\NotificationService;

class OrderStatusUpdatedListener
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(OrderStatusUpdated $event)
    {
        $order = $event->order;
        $this->notificationService->createNotification([
            'user_id' => $order->user_id,
            'type' => 'order_status_updated',
            'data' => [
                'order_id' => $order->id,
                'status' => $order->status,
                'message' => "Your order #{$order->id} has been updated to {$order->status}.",
            ],
        ]);
    }
}
