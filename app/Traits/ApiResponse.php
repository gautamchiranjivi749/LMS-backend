<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
        protected function success(
            string $message = 'Success',
            mixed $data = null,
            int $status = 200
        ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

        protected function error(
        string $message = 'Something went wrong.',
        mixed $errors = null,
        int $status = 500
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}