<?php

namespace Modules\Order\Repositories;

use Modules\Order\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function getAll(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Order;
    public function create(array $data): Order;
    public function update(int $id, array $data): Order;
    public function delete(int $id): bool;
}