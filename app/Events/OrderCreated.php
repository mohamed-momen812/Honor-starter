<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Order\Models\Order;

class OrderCreated
{
    use SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
