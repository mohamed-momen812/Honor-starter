<?php

namespace Modules\Order\Services;

use Modules\Order\Models\Order;
use Modules\Order\Repositories\OrderRepositoryInterface;
use Modules\Product\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrderService
{
    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getAllOrders(): Collection
    {
        return $this->orderRepository->getAll();
    }

    public function getPaginatedOrders(int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->paginate( $perPage);
    }

    public function findOrderById(int $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }

    public function createOrder(array $data, array $items): Order
    {
        return DB::transaction(function () use ($data, $items) {
            $total = 0;
            $orderData = [
                'user_id' => $data['user_id'],
                'status' => $data['status'] ?? 'pending',
                'total' => 0, // Will be updated
            ];

            $order = $this->orderRepository->create($orderData);

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);

                $total += $product->price * $item['quantity'];
                $product->decrement('stock', $item['quantity']);
            }

            $order->update(['total' => $total]);

            return $order;
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