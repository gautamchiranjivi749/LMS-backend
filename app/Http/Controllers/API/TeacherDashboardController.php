<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;use App\Http\Resources\CourseResource;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\QuizAttemptResource;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Auth;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $teacherId = Auth::id();

        // Statistics
        $totalCourses = Course::where('teacher_id', $teacherId)->count();

        $publishedCourses = Course::where('teacher_id', $teacherId)
            ->where('status', true)
            ->count();

        $draftCourses = Course::where('teacher_id', $teacherId)
            ->where('status', false)
            ->count();

        $totalLessons = Lesson::whereHas('course', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->count();

        $totalQuizzes = Quiz::whereHas('course', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->count();

        $totalStudents = Enrollment::whereHas('course', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })
            ->distinct('user_id')
            ->count('user_id');

        $totalCertificates = Certificate::whereHas('course', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->count();

        // Recent Courses
        $recentCourses = Course::where('teacher_id', $teacherId)
            ->latest()
            ->take(5)
            ->get();

        // Recent Enrollments
        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->whereHas('course', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })
            ->latest()
            ->take(5)
            ->get();

        // Recent Quiz Attempts
        $recentQuizAttempts = QuizAttempt::with(['student', 'quiz'])
            ->whereHas('quiz.course', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [

                'statistics' => [

                    'total_courses' => $totalCourses,
                    'published_courses' => $publishedCourses,
                    'draft_courses' => $draftCourses,
                    'total_lessons' => $totalLessons,
                    'total_students' => $totalStudents,
                    'total_quizzes' => $totalQuizzes,
                    'total_certificates' => $totalCertificates,

                ],

                'recent_courses' => CourseResource::collection($recentCourses),

                'recent_enrollments' => EnrollmentResource::collection($recentEnrollments),

                'recent_quiz_attempts' => QuizAttemptResource::collection($recentQuizAttempts),

            ]
        ]);
    }
}