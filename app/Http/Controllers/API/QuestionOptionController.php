<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreQuestionOptionRequest;
use App\Http\Requests\UpdateQuestionOptionRequest;
use App\Http\Resources\QuestionOptionResource;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponse;

class QuestionOptionController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $options = QuestionOption::with('question')
            ->whereHas('question.quiz.course', function ($query) {
                $query->where('teacher_id', Auth::id());
            })
            ->orderBy('option_order')
            ->paginate(10);

        return $this->success(
            'Question options retrieved successfully.',
            QuestionOptionResource::collection($options)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuestionOptionRequest $request)
    {
        $question = Question::where('id', $request->question_id)
            ->whereHas('quiz.course', function ($query) {
                $query->where('teacher_id', Auth::id());
            })
            ->first();

        if (!$question) {
            return $this->error(
                'Unauthorized question.',
                [],
                403
            );
        }

        // Only one correct option for MCQ
        if ($request->boolean('is_correct')) {
            $question->options()->update([
                'is_correct' => false
            ]);
        }

        $option = QuestionOption::create([
            'question_id' => $request->question_id,
            'option_text' => $request->option_text,
            'is_correct' => $request->boolean('is_correct'),
            'option_order' => $request->option_order ?? 1,
        ]);

        return $this->success(
            'Question option created successfully.',
            new QuestionOptionResource(
                $option->load('question')
            ),
            201
        );
    }

    /**
     * Display the specified option.
     */
    public function show(QuestionOption $questionOption)
    {
        if ($questionOption->question->quiz->course->teacher_id != Auth::id()) {
            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        return $this->success(
            'Question option details.',
            new QuestionOptionResource(
                $questionOption->load('question')
            )
        );
    }

    /**
     * Update the specified option.
     */
    public function update(UpdateQuestionOptionRequest $request, QuestionOption $questionOption)
    {
        if ($questionOption->question->quiz->course->teacher_id != Auth::id()) {
            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        if ($request->boolean('is_correct')) {
            $questionOption->question
                ->options()
                ->update([
                    'is_correct' => false
                ]);
        }

        $questionOption->update($request->validated());

        return $this->success(
            'Question option updated successfully.',
            new QuestionOptionResource(
                $questionOption->fresh()->load('question')
            )
        );
    }

    /**
     * Remove the specified option.
     */
    public function destroy(QuestionOption $questionOption)
    {
        if ($questionOption->question->quiz->course->teacher_id != Auth::id()) {
            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        $questionOption->delete();

        return $this->success(
            'Question option deleted successfully.'
        );
    }

    /**
     * Public options for a question.
     */
    public function publicIndex(Question $question)
    {
        $options = $question->options()
            ->orderBy('option_order')
            ->get();

        return $this->success(
            'Question options retrieved successfully.',
            QuestionOptionResource::collection($options)
        );
    }
}
