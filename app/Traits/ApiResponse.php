<?php

namespace App\Traits;

trait ApiResponse
{
    protected function successResponse($data, int $code = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, $code);
    }

    protected function errorResponse(string $message, int $code): \Illuminate\Http\JsonResponse
    {
        return response()->json(['error' => $message], $code);
    }
}