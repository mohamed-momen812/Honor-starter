<?php

namespace Modules\Cart\Repositories;

use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartItem;

class CartRepository implements CartRepositoryInterface
{
    public function findOrCreateByUserId(int $userId): Cart
    {
        return Cart::firstOrCreate(['user_id' => $userId]);
    }

    public function findById(int $id): ?Cart
    {
        return Cart::with('items.product')->find($id);
    }

    public function addItem(int $cartId, array $data): CartItem
    {
        return CartItem::create([
            'cart_id' => $cartId,
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
        ]);
    }

    public function updateItem(int $itemId, array $data): CartItem
    {
        $item = CartItem::find($itemId);
        $item->update($data);
        return $item;
    }

    public function removeItem(int $itemId): bool
    {
        $item = CartItem::find($itemId);
        return $item ? $item->delete() : false;
    }

    public function clearCart(int $cartId): bool
    {
        return CartItem::where('cart_id', $cartId)->delete() > 0;
    }
}