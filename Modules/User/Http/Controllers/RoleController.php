<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\User\Services\RoleService;
use Modules\User\Http\Resources\RoleResource;
use App\Traits\ApiResponse;

class RoleController extends Controller
{
    use ApiResponse;

    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/roles",
     *     tags={"Roles"},
     *     summary="List all roles",
     *     @OA\Response(
     *         response=200,
     *         description="List of roles",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Role")
     *         )
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function index(): JsonResponse
    {
        $roles = $this->roleService->getAllRoles();
        return $this->successResponse(RoleResource::collection($roles));
    }
}