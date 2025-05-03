<?php

namespace Modules\Product\Repositories;

use Modules\Product\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function getAll(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Product;
    public function create(array $data): Product;
    public function update(int $id, array $data): Product;
    public function delete(int $id): bool;
}
