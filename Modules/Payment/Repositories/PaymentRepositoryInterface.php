<?php

namespace Modules\Payment\Repositories;

use Modules\Payment\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PaymentRepositoryInterface
{
    public function getAll(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Payment;
    public function create(array $data): Payment;
    public function update(int $id, array $data): Payment;
    public function delete(int $id): bool;
}
