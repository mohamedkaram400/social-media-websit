<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function apiResponse(string $message, int $status = 200, $data = [], $pagination = null): JsonResponse
    {
        $response = [
            'message' => $message,
            'status_code' => $status,
            'data' => $data,
        ];

        if ($pagination) {
            $response['pagination'] = $pagination;
        }

        return response()->json($response, $status);
    }
}