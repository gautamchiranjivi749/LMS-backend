<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TestController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\UserController;

Route::get('/test', [TestController::class, 'index']);

// Public Routes
Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/courses', [CourseController::class, 'published']);

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
        Route::apiResource('users', UserController::class);    
        });
    });

    // Teacher only
    
    Route::middleware(['auth:sanctum', 'role:teacher'])->group(function () {

        Route::apiResource('teacher/courses', CourseController::class);
    });

    // Student only
    Route::middleware('role:student')->group(function () {
        Route::get('/student/dashboard', function () {
            return response()->json([
                'message' => 'Welcome Student'
            ]);
        });
    });

    //user
    Route::middleware(['auth:sanctum'])->group(function () {

    Route::apiResource('users', UserController::class);

});