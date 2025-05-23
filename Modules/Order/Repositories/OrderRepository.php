<?php

namespace Modules\Order\Repositories;

use Modules\Order\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderRepository implements OrderRepositoryInterface
{
    public function getAll(): Collection
    {
        return Order::with(['user', 'items.product'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $query = QueryBuilder::for(Order::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('status'), // e.g., 'pending', 'completed' , 'delivered'
                AllowedFilter::scope('total_lte', 'whereTotalLessThanOrEqual'),
            ])
            ->allowedSorts(['total', 'created_at', 'status'])
            ->allowedIncludes(['user', 'items.product']); // items.product mean to include product details for each item

        if (!auth()->user()->hasPermissionTo('manage-orders')) {
            $query->where('user_id', auth()->id());
        }

        return $query->paginate($perPage)->appends(request()->query());
    }

    public function findById(int $id): ?Order
    {
        return QueryBuilder::for(Order::class)
            ->allowedIncludes(['user', 'items.product']) // Include user and product details
            ->find($id);
    }

    public function create(array $data, array $orderItems): Order
    {
        $order = Order::create($data);
        foreach ($orderItems as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }
        return $order->load('items.product');
    }

    public function update(int $id, array $data): Order
    {
        $order = $this->findById($id);
        $order->update($data);
        return $order;
    }

    public function delete(int $id): bool
    {
        $order = $this->findById($id);
        return $order ? $order->delete() : false;
    }
}