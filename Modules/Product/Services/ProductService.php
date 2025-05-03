<?php

namespace Modules\Product\Services;

use Modules\Product\Models\Product;
use Modules\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts(): Collection
    {
        return $this->productRepository->getAll();
    }

    public function getPaginatedProducts(int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->paginate($perPage);
    }

    public function findProductById(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }

    public function createProduct(array $data): Product
    {
        $product = $this->productRepository->create($data);
        if (isset($data['category_ids'])) {
            $product->categories()->sync($data['category_ids']);
        }
        return $product;
    }

    public function updateProduct(int $id, array $data): Product
    {
        $product = $this->productRepository->update($id, $data);
        if (isset($data['category_ids'])) {
            $product->categories()->sync($data['category_ids']);
        }
        return $product;
    }

    public function deleteProduct(int $id): bool
    {
        return $this->productRepository->delete($id);
    }
}
