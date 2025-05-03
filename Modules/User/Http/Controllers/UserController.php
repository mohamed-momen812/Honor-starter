<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\User\Http\Requests\StoreUserRequest;
use Modules\User\Http\Requests\UpdateUserRequest;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     tags={"Users"},
     *     summary="Get a list of users",
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);

        $users = $this->userService->getPaginatedUsers( $perPage);
        return $this->successResponse(UserResource::collection($users));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users",
     *     tags={"Users"},
     *     summary="Create a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="New User"),
     *             @OA\Property(property="email", type="string", format="email", example="new@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string", example="admin"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $this->userService->createUser($data, $request->input('roles', []));
        return $this->successResponse(new UserResource($user), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/{id}",
     *     tags={"Users"},
     *     summary="Show a user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->findUserById($id);
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }
        return $this->successResponse(new UserResource($user));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/users/{id}",
     *     tags={"Users"},
     *     summary="Update a user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated User"),
     *             @OA\Property(property="email", type="string", format="email", example="updated@example.com"),
     *             @OA\Property(property="password", type="string", example="newpassword123"),
     *             @OA\Property(property="password_confirmation", type="string", example="newpassword123"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string", example="admin"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $user = $this->userService->updateUser($id, $data, $request->input('roles', []));
        return $this->successResponse(new UserResource($user));
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/users/{id}",
     *     tags={"Users"},
     *     summary="Delete a user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        if (!$this->userService->deleteUser($id)) {
            return $this->errorResponse('User not found', 404);
        }
        return $this->successResponse(['message' => 'User deleted']);
    }
}