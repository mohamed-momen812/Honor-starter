<?php

namespace Modules\Product\Services;

use Modules\Product\Models\Category;
use Modules\Product\Repositories\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories(): Collection
    {
        return $this->categoryRepository->getAll();
    }

    public function getPaginatedCategories(int $perPage = 15): LengthAwarePaginator
    {
        return $this->categoryRepository->paginate($perPage);
    }

    public function findCategoryById(int $id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }

    public function createCategory(array $data): Category
    {
        $data['slug'] = \Str::slug($data['name']);
        return $this->categoryRepository->create($data);
    }

    public function updateCategory(int $id, array $data): Category
    {
        if (isset($data['name'])) {
            $data['slug'] = \Str::slug($data['name']);
        }
        return $this->categoryRepository->update($id, $data);
    }

    public function deleteCategory(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }
}