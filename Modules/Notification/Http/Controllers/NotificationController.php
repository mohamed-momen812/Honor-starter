<?php

namespace Modules\Notification\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Notification\Http\Requests\StoreNotificationRequest;
use Modules\Notification\Http\Requests\UpdateNotificationRequest;
use Modules\Notification\Http\Resources\NotificationResource;
use Modules\Notification\Services\NotificationService;
use App\Traits\ApiResponse;

/**
 * @OA\Get(
 *     path="/api/v1/notifications",
 *     tags={"Notifications"},
 *     summary="List all notifications with filtering and sorting",
 *     @OA\Parameter(
 *         name="filter[id]",
 *         in="query",
 *         description="Filter by exact notification ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="filter[user_id]",
 *         in="query",
 *         description="Filter by user ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="filter[type]",
 *         in="query",
 *         description="Filter by notification type",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="filter[unread]",
 *         in="query",
 *         description="Filter unread notifications",
 *         @OA\Schema(type="boolean")
 *     ),
 *     @OA\Parameter(
 *         name="sort",
 *         in="query",
 *         description="Sort by field (e.g., created_at, -read_at)",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Paginated list of notifications",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Notification")
 *         )
 *     ),
 *     security={{"sanctum":{}}}
 * )
 */
class NotificationController extends Controller
{
    use ApiResponse;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(): JsonResponse
    {
        $notifications = $this->notificationService->getPaginatedNotifications();
        return $this->successResponse(NotificationResource::collection($notifications));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/notifications",
     *     tags={"Notifications"},
     *     summary="Create a new notification",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="type", type="string", example="order_status_updated"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="order_id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="shipped")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Notification created",
     *         @OA\JsonContent(ref="#/components/schemas/Notification")
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function store(StoreNotificationRequest $request): JsonResponse
    {
        $data = $request->validated();
        $notification = $this->notificationService->createNotification($data);
        return $this->successResponse(new NotificationResource($notification), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/notifications/{id}",
     *     tags={"Notifications"},
     *     summary="Show a notification",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification details",
     *         @OA\JsonContent(ref="#/components/schemas/Notification")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Notification not found"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function show(int $id): JsonResponse
    {
        $notification = $this->notificationService->findNotificationById($id);
        if (!$notification) {
            return $this->errorResponse('Notification not found', 404);
        }
        if ($notification->user_id !== auth()->id() && !auth()->user()->hasPermissionTo('manage-notifications')) {
            return $this->errorResponse('Unauthorized', 403);
        }
        return $this->successResponse(new NotificationResource($notification));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/notifications/{id}",
     *     tags={"Notifications"},
     *     summary="Update a notification (e.g., mark as read)",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="read_at", type="string", format="date-time", example="2025-05-04T12:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification updated",
     *         @OA\JsonContent(ref="#/components/schemas/Notification")
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function update(UpdateNotificationRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $notification = $this->notificationService->markNotificationAsRead($id);
        return $this->successResponse(new NotificationResource($notification));
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/notifications/{id}",
     *     tags={"Notifications"},
     *     summary="Delete a notification",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Notification deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Notification not found"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $notification = $this->notificationService->findNotificationById($id);
        if (!$notification) {
            return $this->errorResponse('Notification not found', 404);
        }
        if ($notification->user_id !== auth()->id() && !auth()->user()->hasPermissionTo('manage-notifications')) {
            return $this->errorResponse('Unauthorized', 403);
        }
        $this->notificationService->deleteNotification($id);
        return $this->successResponse(['message' => 'Notification deleted']);
    }
}
