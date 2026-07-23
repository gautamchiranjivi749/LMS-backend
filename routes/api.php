<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TestController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SkillController;



// Public Routes
Route::prefix('public')->group(function () {

    Route::get('/courses', [CourseController::class, 'publicIndex']);

    Route::get('/courses/{course}', [CourseController::class, 'publicShow']);

    Route::get('/latest-courses', [CourseController::class, 'latestCourses']);

    Route::get('/courses/category/{category}', [CourseController::class, 'categoryCourses']);

    Route::get('/courses/skill/{skill}', [CourseController::class, 'skillCourses']);

});

//Login and register
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
    Route::middleware('auth:sanctum','role:admin')->group(function () {
    
        Route::apiResource('users', UserController::class);  

        //  Route::apiResource('skills',SkillController::class);

        // Route::apiResource('categories', CategoryController::class);
    //      Route::apiResource('skills', SkillController::class);

    // Route::patch(
    //     'skills/{id}/restore',
    //     [SkillController::class, 'restore']
    // );

    // Route::delete(
    //     'skills/{id}/force-delete',
    //     [SkillController::class, 'forceDelete']
    // );

        });
    

    // Teacher only
    
    Route::middleware(['auth:sanctum'])->group(function () {

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

    Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('categories', CategoryController::class);

});

});

Route::patch(
    'categories/{id}/restore',
    [CategoryController::class, 'restore']
);

    Route::middleware(['auth:sanctum'])->group(function () {

    Route::apiResource('skills',SkillController::class);

     Route::patch(
        'skills/{id}/restore',
        [SkillController::class, 'restore']
    );

    Route::delete(
        'skills/{id}/force-delete',
        [SkillController::class, 'forceDelete']
    );
    

});