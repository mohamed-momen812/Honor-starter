<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Modules\User\Http\Requests\LoginRequest;
use Modules\User\Services\UserService;

class AuthController extends Controller
{
    use ApiResponse;
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     tags={"Authentication"},
     *     summary="Login and obtain an API token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $token = $this->userService->login($credentials);

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     tags={"Authentication"},
     *     summary="Logout and revoke the API token",
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function logout(): JsonResponse
    {
        $this->userService->logout(auth()->user());

        return $this->successResponse(['message' => 'Successfully logged out']);
    }
}
