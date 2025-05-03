<?php

namespace Modules\Order\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;
use Modules\User\Models\User;
use Modules\Product\Models\Product;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@example.com')->first();
        $product1 = Product::where('sku', 'LAP123')->first();
        $product2 = Product::where('sku', 'SMP456')->first();

        $order = Order::create([
            'user_id' => $user->id,
            'total' => 1699.98,
            'status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'quantity' => 1,
            'price' => $product1->price,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => $product2->price,
        ]);
    }
}