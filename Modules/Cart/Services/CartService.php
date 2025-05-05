<?php

namespace Modules\Cart\Services;

use App\Events\OrderCreated;
use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartItem;
use Modules\Cart\Repositories\CartRepositoryInterface;
use Modules\Order\Services\OrderService;
use Modules\Product\Models\Product;
use Illuminate\Support\Facades\DB;

class CartService
{
    protected $cartRepository;
    protected $orderService;

    public function __construct(
        CartRepositoryInterface $cartRepository,
        OrderService $orderService
    ) {
        $this->cartRepository = $cartRepository;
        $this->orderService = $orderService;
    }

    public function getUserCart(int $userId): Cart
    {
        return $this->cartRepository->findOrCreateByUserId($userId);
    }

    public function addItemToCart(int $userId, array $data): Cart
    {
        $cart = $this->getUserCart($userId);
        $product = Product::findOrFail($data['product_id']);
        if ($product->stock < $data['quantity']) {
            throw new \Exception('Insufficient stock');
        }
        $existingItem = $cart->items()->where('product_id', $data['product_id'])->first();
        if ($existingItem) {
            $this->updateCartItem($existingItem->id, ['quantity' => $existingItem->quantity + $data['quantity']]);
        } else {
            $this->cartRepository->addItem($cart->id, $data);
        }
        return $cart->refresh();
    }

    public function updateCartItem(int $itemId, array $data): CartItem
    {
        $item = CartItem::findOrFail($itemId);
        $product = $item->product;
        if ($data['quantity'] > $product->stock) {
            throw new \Exception('Insufficient stock');
        }
        return $this->cartRepository->updateItem($itemId, $data);
    }

    public function removeCartItem(int $itemId): bool
    {
        return $this->cartRepository->removeItem($itemId);
    }

    public function clearUserCart(int $userId): bool
    {
        $cart = $this->getUserCart($userId);
        return $this->cartRepository->clearCart($cart->id);
    }

    public function checkout(int $userId): \Modules\Order\Models\Order
    {
        return DB::transaction(function () use ($userId) {
            $cart = $this->getUserCart($userId);
            if ($cart->items->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

            $productService = app(\Modules\Product\Services\ProductService::class);
            $orderItems = $cart->items->map(function ($item) use ($productService) {
                $productService->updateStock($item->product_id, $item->quantity);
                return [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'skip_stock_check' => true, // Skip stock check for order items because admin not use this
                ];
            })->toArray();

            $orderData = [
                'user_id' => $userId,
                'total' => $cart->items->sum(fn($item) => $item->quantity * $item->product->price),
                'status' => 'pending',
                'order_items' => $orderItems,
            ];

            $order = $this->orderService->createOrder($orderData);
            $this->cartRepository->clearCart($cart->id);

            // Trigger event
            event(new OrderCreated($order));

            return $order;
        });
    }
}