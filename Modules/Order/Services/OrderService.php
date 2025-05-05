<?php

namespace Modules\Order\Services;

use Modules\Order\Models\Order;
use Modules\Order\Repositories\OrderRepositoryInterface;
use Modules\Product\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getAllOrders()
    {
        return $this->orderRepository->getAll();
    }

    public function getPaginatedOrders(int $perPage = 15)
    {
        return $this->orderRepository->paginate($perPage);
    }

    public function findOrderById(int $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }

    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Validate user_id matches authenticated user for customers
            if (!auth()->user()->hasPermissionTo('manage-orders')) {
                if ($data['user_id'] !== auth()->id()) {
                    throw new \Exception('Unauthorized to create order for another user');
                }
            }

            $orderData = [
                'user_id' => $data['user_id'],
                'total' => $data['total'],
                'status' => $data['status'] ?? 'pending',
            ];

            // Validate order items
            foreach ($data['order_items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                // Skip stock decrement if already handled (e.g., by Cart)
                if (!isset($item['skip_stock_check'])) {
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for product {$product->name}");
                    }
                    $product->decrement('stock', $item['quantity']);
                }
            }

            return $this->orderRepository->create($orderData, $data['order_items']);
        });
    }

    public function updateOrder(int $id, array $data): Order
    {
        $order = $this->orderRepository->findById($id);
        if (isset($data['status']) && $data['status'] !== $order->status) {
            $order->update($data);
            event(new \App\Events\OrderStatusUpdated($order));
        } else {
            $order->update($data);
        }
        return $order;
    }

    public function deleteOrder(int $id): bool
    {
        return $this->orderRepository->delete($id);
    }
}
