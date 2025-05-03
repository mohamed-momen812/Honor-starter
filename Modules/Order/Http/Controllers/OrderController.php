<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Order\Http\Requests\StoreOrderRequest;
use Modules\Order\Http\Requests\UpdateOrderRequest;
use Modules\Order\Http\Resources\OrderResource;
use Modules\Order\Services\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;

    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

   /**
     * @OA\Get(
     *     path="/api/v1/orders",
     *     tags={"Orders"},
     *     summary="List all orders with filtering and sorting",
     *     @OA\Parameter(
     *         name="filter[id]",
     *         in="query",
     *         description="Filter by exact order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filter[user_id]",
     *         in="query",
     *         description="Filter by user ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filter[status]",
     *         in="query",
     *         description="Filter by status",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filter[total_lte]",
     *         in="query",
     *         description="Filter by total less than or equal",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort by field (e.g., total, -created_at)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of orders",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Order")
     *         )
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);

        $orders = $this->orderService->getPaginatedOrders($perPage);
        return $this->successResponse(OrderResource::collection($orders));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/orders",
     *     tags={"Orders"},
     *     summary="Create a new order",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="pending"),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=2)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $order = $this->orderService->createOrder($data, $data['items']);
        return $this->successResponse(new OrderResource($order), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/orders/{id}",
     *     tags={"Orders"},
     *     summary="Show an order",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order details",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->findOrderById($id);
        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }
        return $this->successResponse(new OrderResource($order));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/orders/{id}",
     *     tags={"Orders"},
     *     summary="Update an order",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="shipped")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $order = $this->orderService->updateOrder($id, $data);
        return $this->successResponse(new OrderResource($order));
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/orders/{id}",
     *     tags={"Orders"},
     *     summary="Delete an order",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Order deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        if (!$this->orderService->deleteOrder($id)) {
            return $this->errorResponse('Order not found', 404);
        }
        return $this->successResponse(['message' => 'Order deleted']);
    }
}
