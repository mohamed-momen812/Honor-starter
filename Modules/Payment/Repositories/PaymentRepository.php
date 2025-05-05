<?php

namespace Modules\Payment\Repositories;

use Modules\Payment\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function getAll(): Collection
    {
        return Payment::with(['user', 'order'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $query = QueryBuilder::for(Payment::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('order_id'),
                AllowedFilter::exact('status'),
            ])
            ->allowedSorts(['amount', 'created_at'])
            ->allowedIncludes(['user', 'order']);

        if (!auth()->user()->hasPermissionTo('manage-payments')) {
            $query->where('user_id', auth()->id());
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Payment
    {
        return QueryBuilder::for(Payment::class)
            ->allowedIncludes(['user', 'order'])
            ->findOrFail($id);
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function update(int $id, array $data): Payment
    {
        $payment = $this->findById($id);
        $payment->update($data);
        return $payment;
    }

    public function delete(int $id): bool
    {
        $payment = $this->findById($id);
        return $payment ? $payment->delete() : false;
    }
}