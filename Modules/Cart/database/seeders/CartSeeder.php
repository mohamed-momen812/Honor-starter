<?php

namespace Modules\Cart\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartItem;
use Modules\User\Models\User;
use Modules\Product\Models\Product;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('email', 'user@example.com')->first();
        $product = Product::first();

        $cart = Cart::create(['user_id' => $customer->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }
}
