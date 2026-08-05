<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Support\Facades\Auth;

class LessonProgressController extends Controller
{
    public function complete(Lesson $lesson)
{
    $studentId = Auth::id();

    $isEnrolled = Enrollment::where('user_id', $studentId)
        ->where('course_id', $lesson->course_id)
        ->exists();

    if (!$isEnrolled) {

        return response()->json([
            'success' => false,
            'message' => 'You are not enrolled in this course.'
        ], 403);
    }

    $progress = LessonProgress::updateOrCreate(

        [
            'user_id' => $studentId,
            'lesson_id' => $lesson->id,
        ],

        [
            'completed' => true,
            'completed_at' => now(),
        ]

    );

    return response()->json([
        'success' => true,
        'message' => 'Lesson completed successfully.',
        'data' => $progress
    ]);
}

public function courseProgress(Course $course)
{
    $studentId = Auth::id();

    $isEnrolled = Enrollment::where('user_id', $studentId)
        ->where('course_id', $course->id)
        ->exists();

    if (!$isEnrolled) {

        return response()->json([
            'success' => false,
            'message' => 'You are not enrolled in this course.'
        ], 403);
    }

    $totalLessons = $course->lessons()
        ->where('status', true)
        ->count();

    $completedLessons = LessonProgress::where('user_id', $studentId)
        ->whereHas('lesson', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })
        ->where('completed', true)
        ->count();

    $percentage = $totalLessons > 0
        ? round(($completedLessons / $totalLessons) * 100, 2)
        : 0;

    return response()->json([
        'success' => true,
        'data' => [

            'course_id' => $course->id,

            'course_title' => $course->title,

            'completed_lessons' => $completedLessons,

            'total_lessons' => $totalLessons,

            'progress_percentage' => $percentage,

            'remaining_lessons' => $totalLessons - $completedLessons,

        ]
    ]);
}

public function myProgress()
{
    $studentId = Auth::id();

    $courses = Enrollment::with('course.lessons')
        ->where('user_id', $studentId)
        ->get();

    $progress = [];

    foreach ($courses as $enrollment) {

        $course = $enrollment->course;

        $totalLessons = $course->lessons
            ->where('status', true)
            ->count();

        $completedLessons = LessonProgress::where('user_id', $studentId)
            ->whereHas('lesson', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->where('completed', true)
            ->count();

        $progress[] = [

            'course_id' => $course->id,

            'course_title' => $course->title,

            'completed_lessons' => $completedLessons,

            'total_lessons' => $totalLessons,

            'progress_percentage' => $totalLessons > 0
                ? round(($completedLessons / $totalLessons) * 100, 2)
                : 0,

        ];
    }

    return response()->json([
        'success' => true,
        'data' => $progress
    ]);
}

}
