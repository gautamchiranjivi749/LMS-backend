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
use App\Http\Controllers\API\QuizController;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\QuestionOptionController;
use App\Http\Controllers\API\QuizAttemptController;
use App\Http\Controllers\API\CertificateController;
use App\Http\Controllers\API\StudentDashboardController;
use App\Http\Controllers\API\TeacherDashboardController;
use App\Http\Controllers\API\AdminDashboardController;
use App\Http\Controllers\API\LessonProgressController;
use App\Http\Controllers\API\ReviewController;
use Illuminate\Support\Facades\Route;




// Public Routes
Route::prefix('public')->group(function () {

    Route::get('/courses', [CourseController::class, 'publicIndex']);

    Route::get('/courses/{course}', [CourseController::class, 'publicShow']);

    Route::get('/latest-courses', [CourseController::class, 'latestCourses']);

    Route::get('/courses/category/{category}', [CourseController::class, 'categoryCourses']);

    Route::get('/courses/skill/{skill}', [CourseController::class, 'skillCourses']);

    Route::get('courses/{course}/lessons',[LessonController::class, 'publicIndex']);

    Route::get('lessons/{lesson}',[LessonController::class, 'publicShow']);

    Route::get('courses/{course}/quizzes',[QuizController::class, 'publicIndex']);

    Route::get('quizzes/{quiz}',[QuizController::class, 'publicShow']);

    Route::get('quizzes/{quiz}/questions',[QuestionController::class, 'publicIndex']);

    Route::get('questions/{question}/options',[QuestionOptionController::class, 'publicIndex']);

    Route::get('courses/{course}/reviews',[ReviewController::class, 'index']);

    Route::get('reviews/{review}',[ReviewController::class, 'show']    );

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

        Route::get('dashboard', [AdminDashboardController::class, 'index']);

        });
    

    // Teacher only
    
    Route::middleware(['auth:sanctum','role:Teacher'])->prefix('teacher')
    ->group(function () {

        Route::apiResource('courses', CourseController::class);

        Route::apiResource('lessons',LessonController::class);

        Route::apiResource('quizzes', QuizController::class);

        Route::apiResource('questions', QuestionController::class);

        Route::apiResource('question-options', QuestionOptionController::class);

        Route::get('dashboard', [TeacherDashboardController::class, 'index']);
    });

    // Student only
       Route::middleware(['auth:sanctum', 'role:Student'])
        ->prefix('student')->group(function () {

        Route::apiResource('enrollments', EnrollmentController::class);

         Route::post('quizzes/{quiz}/start',[QuizAttemptController::class, 'start']);

        Route::post('quizzes/{quiz}/submit',[QuizAttemptController::class, 'submit'] );

        Route::get('quizzes/{quiz}/result',[QuizAttemptController::class, 'result']);

        Route::get('quiz-history',[QuizAttemptController::class, 'history']);
        
        Route::apiResource('certificates', CertificateController::class)->only(['index', 'show']);

        Route::get('certificates/{certificate}/download',[CertificateController::class,'download']);

         Route::post('lessons/{lesson}/complete',[LessonProgressController::class, 'complete']);

        Route::get('courses/{course}/progress',[LessonProgressController::class, 'courseProgress']);

        Route::get('my-progress',[LessonProgressController::class, 'myProgress']);  
        
         Route::post('courses/{course}/review',[ReviewController::class, 'store']); 

        Route::get('courses/{course}/reviews',[ReviewController::class, 'index']);
        
        Route::put('reviews/{review}',[ReviewController::class, 'update']);

        Route::delete('reviews/{review}',[ReviewController::class, 'destroy']);


       


        Route::get('dashboard', [StudentDashboardController::class, 'index']);
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