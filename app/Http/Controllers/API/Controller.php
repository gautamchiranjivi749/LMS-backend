<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function success(
        string $message,
        mixed $data = null,
        int $status = 200
    ) {
        return ApiResponse::success($message, $data, $status);
    }

    protected function error(
        string $message,
        mixed $errors = null,
        int $status = 500
    ) {
        return ApiResponse::error($message, $errors, $status);
    }
}