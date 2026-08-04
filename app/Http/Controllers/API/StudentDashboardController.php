<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $studentId = Auth::id();

        $enrolledCourses = Enrollment::where('user_id', $studentId)->count();

        $completedCourses = Enrollment::where('user_id', $studentId)
            ->where('status', 'completed')
            ->count();

        $recentCourses = Enrollment::with('course')
        ->where('user_id', $studentId)
        ->latest()
        ->take(5)
        ->get();

        $recentCertificates = Certificate::with('course')
        ->where('user_id', $studentId)
        ->latest()
        ->take(5)
        ->get();

        $recentQuizzes = QuizAttempt::with('quiz')
        ->where('student_id', $studentId)
        ->latest()
        ->take(5)
        ->get();

        $certificates = Certificate::where('user_id', $studentId)->count();

        $quizAttempts = QuizAttempt::where('student_id', $studentId)
            ->where('status', 'submitted')
            ->count();

        $averageScore = QuizAttempt::where('student_id', $studentId)
            ->where('status', 'submitted')
            ->avg('percentage');

        return response()->json([
            'success' => true,
            'data' => [
                'student' => Auth::user()->name,
                'total_enrolled_courses' => $enrolledCourses,
                'completed_courses' => $completedCourses,
                'certificates' => $certificates,
                'quiz_attempts' => $quizAttempts,
                'average_score' => round($averageScore ?? 0, 2),
            ]
        ]);
    }
}
