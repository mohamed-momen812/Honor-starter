<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Product\Http\Requests\StoreProductRequest;
use Modules\Product\Http\Requests\UpdateProductRequest;
use Modules\Product\Http\Resources\ProductResource;
use Modules\Product\Services\ProductService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }


    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     tags={"Products"},
     *     summary="List all products with filtering and sorting",
     *     @OA\Parameter(
     *         name="filter[id]",
     *         in="query",
     *         description="Filter by exact product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filter[name]",
     *         in="query",
     *         description="Filter by partial product name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filter[sku]",
     *         in="query",
     *         description="Filter by partial SKU",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filter[price]",
     *         in="query",
     *         description="Filter by exact price",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="filter[price_lte]",
     *         in="query",
     *         description="Filter by price less than or equal",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="filter[price_gte]",
     *         in="query",
     *         description="Filter by price greater than or equal",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="filter[stock]",
     *         in="query",
     *         description="Filter by exact stock",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filter[category_id]",
     *         in="query",
     *         description="Filter by category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort by field (e.g., name, -price)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of products",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
     *         )
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
   public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);

        $products = $this->productService->getPaginatedProducts( $perPage);

        return $this->successResponse(ProductResource::collection($products));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     tags={"Products"},
     *     summary="Create a new product",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Laptop"),
     *             @OA\Property(property="description", type="string", example="High-performance laptop"),
     *             @OA\Property(property="price", type="number", example=999.99),
     *             @OA\Property(property="stock", type="integer", example=100),
     *             @OA\Property(property="sku", type="string", example="LAP123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        $product = $this->productService->createProduct($data);
        return $this->successResponse(new ProductResource($product), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{id}",
     *     tags={"Products"},
     *     summary="Show a product",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product details",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->findProductById($id);
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }
        return $this->successResponse(new ProductResource($product));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/products/{id}",
     *     tags={"Products"},
     *     summary="Update a product",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Laptop"),
     *             @OA\Property(property="description", type="string", example="Updated description"),
     *             @OA\Property(property="price", type="number", example=1099.99),
     *             @OA\Property(property="stock", type="integer", example=50),
     *             @OA\Property(property="sku", type="string", example="LAP456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $product = $this->productService->updateProduct($id, $data);
        return $this->successResponse(new ProductResource($product));
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/products/{id}",
     *     tags={"Products"},
     *     summary="Delete a product",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        if (!$this->productService->deleteProduct($id)) {
            return $this->errorResponse('Product not found', 404);
        }
        return $this->successResponse(['message' => 'Product deleted']);
    }
}
