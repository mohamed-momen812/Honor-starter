<?php

namespace Modules\Product\Repositories;

use Modules\Product\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function getAll(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Category;
    public function create(array $data): Category;
    public function update(int $id, array $data): Category;
    public function delete(int $id): bool;
}
