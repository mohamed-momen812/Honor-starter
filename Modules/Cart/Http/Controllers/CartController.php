<?php

namespace Modules\Cart\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Cart\Http\Requests\AddToCartRequest;
use Modules\Cart\Http\Requests\UpdateCartItemRequest;
use Modules\Cart\Http\Resources\CartItemResource;
use Modules\Cart\Http\Resources\CartResource;
use Modules\Cart\Services\CartService;
use Modules\Order\Http\Resources\OrderResource;
use App\Traits\ApiResponse;


class CartController extends Controller
{
    use ApiResponse;

    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/cart",
     *     tags={"Cart"},
     *     summary="Get user cart",
     *     @OA\Response(
     *         response=200,
     *         description="User cart details",
     *         @OA\JsonContent(ref="#/components/schemas/Cart")
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function index(): JsonResponse
    {
        $cart = $this->cartService->getUserCart(auth()->id());
        return $this->successResponse(new CartResource($cart));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/cart",
     *     tags={"Cart"},
     *     summary="Add item to cart",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Item added to cart",
     *         @OA\JsonContent(ref="#/components/schemas/Cart")
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function store(AddToCartRequest $request): JsonResponse
    {
        $data = $request->validated();
        $cart = $this->cartService->addItemToCart(auth()->id(), $data);
        return $this->successResponse(new CartResource($cart), 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/cart/items/{id}",
     *     tags={"Cart"},
     *     summary="Update cart item quantity",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="quantity", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart item updated",
     *         @OA\JsonContent(ref="#/components/schemas/CartItem")
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function update(UpdateCartItemRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $item = $this->cartService->updateCartItem($id, $data);
        return $this->successResponse(new CartItemResource($item));
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/cart/items/{id}",
     *     tags={"Cart"},
     *     summary="Remove item from cart",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item removed from cart",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Item removed")
     *         )
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->cartService->removeCartItem($id);
        return $this->successResponse(['message' => 'Item removed']);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/cart/checkout",
     *     tags={"Cart"},
     *     summary="Checkout cart to create an order",
     *     @OA\Response(
     *         response=201,
     *         description="Order created from cart",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function checkout(): JsonResponse
    {
        $order = $this->cartService->checkout(auth()->id());
        return $this->successResponse(new OrderResource($order), 201);
    }
}
