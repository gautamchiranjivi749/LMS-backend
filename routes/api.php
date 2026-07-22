<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TestController;

Route::get('/test', [TestController::class, 'index']);

// Public Routes
Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);
});

// Protected Routes
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', [AuthController::class, 'me']);
});

  // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return response()->json([
                'message' => 'Welcome Admin'
            ]);
        });
    });

    // Teacher only
    Route::middleware('role:teacher')->group(function () {
        Route::get('/teacher/dashboard', function () {
            return response()->json([
                'message' => 'Welcome Teacher'
            ]);
        });
    });

    // Student only
    Route::middleware('role:student')->group(function () {
        Route::get('/student/dashboard', function () {
            return response()->json([
                'message' => 'Welcome Student'
            ]);
        });
    });