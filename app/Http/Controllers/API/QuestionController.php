<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Models\Quiz;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
           $questions = Question::with('quiz')
            ->whereHas('quiz.course', function ($query) {
                $query->where('teacher_id', Auth::id());
            })
            ->orderBy('question_order')
            ->paginate(10);

        return $this->success(
            'Questions retrieved successfully.',
            QuestionResource::collection($questions)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(StoreQuestionRequest $request)
    {
        $quiz = Quiz::where('id', $request->quiz_id)
            ->whereHas('course', function ($query) {
                $query->where('teacher_id', Auth::id());
            })
            ->first();

        if (!$quiz) {
            return $this->error(
                'Unauthorized quiz.',
                [],
                403
            );
        }

        $question = Question::create([
            'quiz_id' => $request->quiz_id,
            'question' => $request->question,
            'question_type' => $request->question_type,
            'marks' => $request->marks,
            'question_order' => $request->question_order ?? 1,
            'status' => $request->boolean('status'),
        ]);

        return $this->success(
            'Question created successfully.',
            new QuestionResource($question->load('quiz')),
            201
        );
    }

    /**
     * Display the specified resource.
     */
     public function show(Question $question)
    {
        if ($question->quiz->course->teacher_id != Auth::id()) {
            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        return $this->success(
            'Question details.',
            new QuestionResource($question->load('quiz'))
        );
    }

    /**
     * Update question.
     */
    public function update(UpdateQuestionRequest $request, Question $question)
    {
        if ($question->quiz->course->teacher_id != Auth::id()) {
            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        $question->update($request->validated());

        return $this->success(
            'Question updated successfully.',
            new QuestionResource($question->fresh()->load('quiz'))
        );
    }

    /**
     * Delete question.
     */
    public function destroy(Question $question)
    {
        if ($question->quiz->course->teacher_id != Auth::id()) {
            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        $question->delete();

        return $this->success(
            'Question deleted successfully.'
        );
    }

    /**
     * Public questions for a quiz.
     */
    public function publicIndex(Quiz $quiz)
    {
        $questions = $quiz->questions()
            ->where('status', true)
            ->orderBy('question_order')
            ->get();

        return $this->success(
            'Questions retrieved successfully.',
            QuestionResource::collection($questions)
        );
    }
}
