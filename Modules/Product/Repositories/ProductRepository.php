<?php

namespace Modules\Product\Repositories;

use Modules\Product\Filters\StockRangeFilter;
use Modules\Product\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAll(): Collection
    {
        return Product::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::exact('id'), // exact match
                AllowedFilter::partial('name'), // match any part of the name
                AllowedFilter::partial('sku'), // match any part of the SKU
                AllowedFilter::exact('price'), // exact match
                AllowedFilter::scope('price_lte', 'wherePriceLessThanOrEqual'), // custom scope
                AllowedFilter::scope('price_gte', 'wherePriceGreaterThanOrEqual'), // custom scope
                AllowedFilter::exact('stock'),  // exact match
                AllowedFilter::exact('categories.id'),
                AllowedFilter::custom('stock_range', new StockRangeFilter), // custom filter
            ])
            ->allowedSorts(['name', 'price', 'stock', 'created_at'])
            ->allowedIncludes(['categories'])
            ->paginate($perPage)
            ->appends(request()->query()); // Append filters to pagination links
    }

    public function findById(int $id): ?Product
    {
        return QueryBuilder::for(Product::class)
            ->allowedIncludes(['categories'])
            ->find($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->findById($id);
        $product->update($data);
        return $product;
    }

    public function delete(int $id): bool
    {
        $product = $this->findById($id);
        return $product ? $product->delete() : false;
    }
}
