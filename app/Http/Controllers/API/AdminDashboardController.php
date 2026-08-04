<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CourseResource;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Spatie\Permission\Traits\HasRoles;

class AdminDashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $statistics = [

           
    'total_students' => User::role('student')->count(),

    'total_teachers' => User::role('teacher')->count(),

    'total_admins' => User::role('admin')->count(),

    'total_categories' => Category::count(),

    'total_courses' => Course::count(),

    'published_courses' => Course::where('status', true)->count(),

    'draft_courses' => Course::where('status', false)->count(),

    'total_lessons' => Lesson::count(),

    'total_enrollments' => Enrollment::count(),

    'total_quizzes' => Quiz::count(),

    'total_quiz_attempts' => QuizAttempt::count(),

    'total_certificates' => Certificate::count(),

        ];

        /*
        |--------------------------------------------------------------------------
        | Recent Students
        |--------------------------------------------------------------------------
        */

       $recentStudents = User::role('student')
    ->latest()
    ->take(5)
    ->get();

        /*
        |--------------------------------------------------------------------------
        | Recent Teachers
        |--------------------------------------------------------------------------
        */

       $recentTeachers = User::role('teacher')
    ->latest()
    ->take(5)
    ->get();

        /*
        |--------------------------------------------------------------------------
        | Recent Courses
        |--------------------------------------------------------------------------
        */

        $recentCourses = Course::with(['teacher', 'category'])
            ->latest()
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Recent Enrollments
        |--------------------------------------------------------------------------
        */

        $recentEnrollments = Enrollment::with([
            'user',
            'course',
        ])
            ->latest()
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Recent Certificates
        |--------------------------------------------------------------------------
        */

        $recentCertificates = Certificate::with([
            'user',
            'course',
        ])
            ->latest()
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Top Courses
        |--------------------------------------------------------------------------
        */

        $topCourses = Course::withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->take(5)
            ->get();

        return response()->json([

            'success' => true,

            'message' => 'Admin dashboard loaded successfully.',

            'data' => [

                'statistics' => $statistics,

                'recent_students' => UserResource::collection($recentStudents),

                'recent_teachers' => UserResource::collection($recentTeachers),

                'recent_courses' => CourseResource::collection($recentCourses),

                'recent_enrollments' => EnrollmentResource::collection($recentEnrollments),

                'recent_certificates' => $recentCertificates,

                'top_courses' => CourseResource::collection($topCourses),

            ]

        ]);
    }
}