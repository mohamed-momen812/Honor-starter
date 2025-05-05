<?php

namespace Modules\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Payment\Http\Requests\CreatePaymentRequest;
use Modules\Payment\Http\Resources\PaymentResource;
use Modules\Payment\Services\PaymentService;
use App\Traits\ApiResponse;


class PaymentController extends Controller
{
    use ApiResponse;

    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/payments",
     *     tags={"Payments"},
     *     summary="List all payments with filtering and sorting",
     *     @OA\Parameter(
     *         name="filter[id]",
     *         in="query",
     *         description="Filter by exact payment ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filter[user_id]",
     *         in="query",
     *         description="Filter by user ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filter[order_id]",
     *         in="query",
     *         description="Filter by order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filter[status]",
     *         in="query",
     *         description="Filter by payment status",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort by field (e.g., amount, -created_at)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of payments",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Payment")
     *         )
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function index(): JsonResponse
    {
        $perPage = request()->get('per_page', 10);

        $payments = $this->paymentService->getPaginatedPayments($perPage);
        return $this->successResponse(PaymentResource::collection($payments));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/payments",
     *     tags={"Payments"},
     *     summary="Create a payment intent for an order",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="order_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment intent created",
     *         @OA\JsonContent(
     *             @OA\Property(property="client_secret", type="string"),
     *             @OA\Property(property="payment_id", type="integer")
     *         )
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function store(CreatePaymentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->paymentService->createPaymentIntent($data['order_id']);
        return $this->successResponse($result, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/payments/{id}",
     *     tags={"Payments"},
     *     summary="Show a payment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment details",
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function show(int $id): JsonResponse
    {
        $payment = $this->paymentService->findPaymentById($id);
        if (!$payment) {
            return $this->errorResponse('Payment not found', 404);
        }
        if ($payment->user_id !== auth()->id() && !auth()->user()->hasPermissionTo('manage-payments')) {
            return $this->errorResponse('Unauthorized', 403);
        }
        return $this->successResponse(new PaymentResource($payment));
    }
}
