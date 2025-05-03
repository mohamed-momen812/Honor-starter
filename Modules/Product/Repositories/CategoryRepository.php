<?php

namespace Modules\Product\Repositories;

use Modules\Product\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAll(): Collection
    {
        return Category::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(Category::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('slug'),
                AllowedFilter::partial('description'),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts(['name', 'created_at'])
            ->paginate($perPage)
            ->appends(request()->query());
    }


    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(int $id, array $data): Category
    {
        $category = $this->findById($id);
        $category->update($data);
        return $category;
    }

    public function delete(int $id): bool
    {
        $category = $this->findById($id);
        return $category ? $category->delete() : false;
    }
}