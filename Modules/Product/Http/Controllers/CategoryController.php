<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Product\Http\Requests\StoreCategoryRequest;
use Modules\Product\Http\Requests\UpdateCategoryRequest;
use Modules\Product\Http\Resources\CategoryResource;
use Modules\Product\Services\CategoryService;
use App\Traits\ApiResponse;

class CategoryController extends Controller
{
    use ApiResponse;

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }


    /**
     * @OA\Get(
     *     path="/api/v1/categories",
     *     tags={"Categories"},
     *     summary="List all categories with filtering and sorting",
     *     @OA\Parameter(
     *         name="filter[name]",
     *         in="query",
     *         description="Filter by partial category name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filter[slug]",
     *         in="query",
     *         description="Filter by exact slug",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort by field (e.g., name, -created_at)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of categories",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Category")
     *         )
     *     ),
     *     security={{"sanctum":{}}}
     * )
    */
    public function index(): JsonResponse
    {
        $perPage = request()->get('per_page', 15);

        $categories = $this->categoryService->getPaginatedCategories($perPage);
        return $this->successResponse(CategoryResource::collection($categories));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/categories",
     *     tags={"Categories"},
     *     summary="Create a new category",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Electronics"),
     *             @OA\Property(property="description", type="string", example="Electronic devices")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $category = $this->categoryService->createCategory($data, $request->input('products', []));
        return $this->successResponse(new CategoryResource($category), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories/{id}",
     *     tags={"Categories"},
     *     summary="Show a category",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category details",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->findCategoryById($id);
        if (!$category) {
            return $this->errorResponse('Category not found', 404);
        }
        return $this->successResponse(new CategoryResource($category));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/categories/{id}",
     *     tags={"Categories"},
     *     summary="Update a category",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Electronics"),
     *             @OA\Property(property="description", type="string", example="Updated description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $category = $this->categoryService->updateCategory($id, $data);
        return $this->successResponse(new CategoryResource($category));
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/categories/{id}",
     *     tags={"Categories"},
     *     summary="Delete a category",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        if (!$this->categoryService->deleteCategory($id)) {
            return $this->errorResponse('Category not found', 404);
        }
        return $this->successResponse(['message' => 'Category deleted']);
    }
}