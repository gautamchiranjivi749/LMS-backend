<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TestController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SkillController;
use App\Http\Controllers\API\LessonController;
use App\Http\Controllers\API\EnrollmentController;




// Public Routes
Route::prefix('public')->group(function () {

    Route::get('/courses', [CourseController::class, 'publicIndex']);

    Route::get('/courses/{course}', [CourseController::class, 'publicShow']);

    Route::get('/latest-courses', [CourseController::class, 'latestCourses']);

    Route::get('/courses/category/{category}', [CourseController::class, 'categoryCourses']);

    Route::get('/courses/skill/{skill}', [CourseController::class, 'skillCourses']);

    Route::get(
    'courses/{course}/lessons',
    [LessonController::class, 'publicIndex']
);

Route::get(
    'lessons/{lesson}',
    [LessonController::class, 'publicShow']
);

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
    Route::middleware('auth:sanctum','role:Admin')->group(function () {
    
        Route::apiResource('users', UserController::class);  

        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('skills', SkillController::class);

        Route::patch('categories/{id}/restore', [CategoryController::class, 'restore']);
        Route::patch('skills/{id}/restore', [SkillController::class, 'restore']);
        Route::delete('skills/{id}/force-delete', [SkillController::class, 'forceDelete']);

        });
    

    // Teacher only
    
    Route::middleware(['auth:sanctum','role:Teacher'])->prefix('teacher')->group(function () {

        Route::apiResource('courses', CourseController::class);

        Route::apiResource('lessons',LessonController::class);
    });

    // Student only
       Route::middleware(['auth:sanctum', 'role:Student'])
        ->prefix('student')->group(function () {

        Route::apiResource('enrollments', EnrollmentController::class);

    });

    //user
    Route::middleware(['auth:sanctum'])->group(function () {

    Route::apiResource('users', UserController::class);

    Route::apiResource('categories', CategoryController::class);

});


   
Route::middleware('auth:sanctum')->get('/test-auth', function () {
    return response()->json([
        'id' => auth()->id(),
        'user' => auth()->user(),
        'roles' => auth()->user()?->getRoleNames(),
    ]);
});