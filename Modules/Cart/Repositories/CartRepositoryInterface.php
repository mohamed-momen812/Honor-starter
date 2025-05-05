<?php

namespace Modules\Cart\Repositories;

use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartItem;

interface CartRepositoryInterface
{
    public function findOrCreateByUserId(int $userId): Cart;
    public function findById(int $id): ?Cart;
    public function addItem(int $cartId, array $data): CartItem;
    public function updateItem(int $itemId, array $data): CartItem;
    public function removeItem(int $itemId): bool;
    public function clearCart(int $cartId): bool;
}
