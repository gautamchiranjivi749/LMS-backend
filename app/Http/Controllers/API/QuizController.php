<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use App\Models\Course;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
          $quizzes = Quiz::with('course')
            ->whereHas('course', function ($query) {
                $query->where('teacher_id', Auth::id());
            })
            ->latest()
            ->paginate(10);

        return $this->success(
            'Quiz list retrieved successfully.',
            QuizResource::collection($quizzes)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
       public function store(StoreQuizRequest $request)
    {
        $course = Course::where('id', $request->course_id)
            ->where('teacher_id', Auth::id())
            ->first();

        if (!$course) {
            return $this->error(
                'Unauthorized course.',
                [],
                403
            );
        }

        $quiz = Quiz::create([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'description' => $request->description,
            'time_limit' => $request->time_limit,
            'passing_marks' => $request->passing_marks,
            'total_marks' => $request->total_marks,
            'status' => $request->boolean('status'),
        ]);

        return $this->success(
            'Quiz created successfully.',
            new QuizResource($quiz->load('course')),
            201
        );
    }

    /**
     * Display the specified resource.
     */
     public function show(Quiz $quiz)
    {
        if ($quiz->course->teacher_id != Auth::id()) {
            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        return $this->success(
            'Quiz details.',
            new QuizResource($quiz->load('course'))
        );
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(UpdateQuizRequest $request, Quiz $quiz)
    {
        if ($quiz->course->teacher_id != Auth::id()) {
            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        $quiz->update($request->validated());

        return $this->success(
            'Quiz updated successfully.',
            new QuizResource($quiz->fresh()->load('course'))
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $quiz)
    {
        if ($quiz->course->teacher_id != Auth::id()) {
            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        $quiz->delete();

        return $this->success(
            'Quiz deleted successfully.'
        );
    }
     public function publicIndex(Course $course)
    {
        $quizzes = $course->quizzes()
            ->where('status', true)
            ->latest()
            ->get();

     return $this->success(
            'Quiz list.',
            QuizResource::collection($quizzes)
        );
    }

    /**
     * Public quiz details
     */
    public function publicShow(Quiz $quiz)
    {
        if (!$quiz->status) {
            return $this->error(
                'Quiz not found.',
                [],
                404
            );
        }

        return $this->success(
            'Quiz details.',
            new QuizResource($quiz->load('course'))
        );
    }
}
