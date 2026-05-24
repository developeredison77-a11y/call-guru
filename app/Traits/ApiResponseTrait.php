<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function successResponse(array $data = [], string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json(array_merge([
            'success' => true,
            'message' => $message,
        ], $data), $status);
    }

    protected function errorResponse(string $message, int $status = 422, array $errors = []): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if (! empty($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }
}

