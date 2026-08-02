<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SubmitQuizRequest;
use App\Http\Resources\QuizAttemptResource;
use App\Models\Enrollment;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\StudentAnswer;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizAttemptController extends Controller
{
     use ApiResponse;

    /**
     * Start a quiz attempt.
     */
    public function start(Quiz $quiz)
    {$totalMarks = $quiz->questions()
    ->where('status', true)
    ->sum('marks');



        $studentId = Auth::id();

        $isEnrolled = Enrollment::where('user_id', $studentId)
            ->where('course_id', $quiz->course_id)
            ->exists();

        if (!$isEnrolled) {
            return $this->error(
                'You must enroll in this course before starting the quiz.',
                [],
                403
            );
        }

        if (!$quiz->status) {
            return $this->error(
                'This quiz is currently unavailable.',
                [],
                403
            );
        }

        $existingAttempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $studentId)
            ->where('status', 'started')
            ->first();

        if ($existingAttempt) {
            return $this->success(
                'You already have an active quiz attempt.',
                new QuizAttemptResource(
                    $existingAttempt->load('quiz')
                )
            );
        }

        $totalMarks = $quiz->questions()
            ->where('status', true)
            ->sum('marks');



        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $studentId,
            'score' => 0,
            'total_marks' => $totalMarks,
            'percentage' => 0,
            'status' => 'started',
            'started_at' => now(),
        ]);
       

        return $this->success(
            'Quiz started successfully.',
            new QuizAttemptResource(
                $attempt->load('quiz')
            ),
            201
        );
    }

    /**
     * Submit quiz answers.
     */
    public function submit(SubmitQuizRequest $request, Quiz $quiz)
    {
        $attempt = QuizAttempt::where('id', $request->attempt_id)
            ->where('quiz_id', $quiz->id)
            ->where('student_id', Auth::id())
            ->where('status', 'started')
            ->first();

        if (!$attempt) {
            return $this->error(
                'Active quiz attempt not found.',
                [],
                404
            );
        }

        try {
            DB::beginTransaction();

            $score = 0;

            foreach ($request->answers as $submittedAnswer) {
                $question = $quiz->questions()
                    ->where('id', $submittedAnswer['question_id'])
                    ->where('status', true)
                    ->first();

                if (!$question) {
                    continue;
                }

                $isCorrect = false;
                $marksObtained = 0;
                $optionId = $submittedAnswer['question_option_id'] ?? null;
                $answerText = $submittedAnswer['answer_text'] ?? null;

                if (
                    in_array(
                        $question->question_type,
                        ['mcq', 'true_false']
                    ) &&
                    $optionId
                ) {
                    $selectedOption = QuestionOption::where('id', $optionId)
                        ->where('question_id', $question->id)
                        ->first();

                    if ($selectedOption && $selectedOption->is_correct) {
                        $isCorrect = true;
                        $marksObtained = $question->marks;
                    }
                }

                StudentAnswer::updateOrCreate(
                    [
                        'quiz_attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                    ],
                    [
                        'question_option_id' => $optionId,
                        'answer_text' => $answerText,
                        'is_correct' => $isCorrect,
                        'marks_obtained' => $marksObtained,
                    ]
                );

                $score += $marksObtained;
            }

            $percentage = $attempt->total_marks > 0
                ? ($score / $attempt->total_marks) * 100
                : 0;

            $attempt->update([
                'score' => $score,
                'percentage' => round($percentage, 2),
                'status' => 'submitted',
                'completed_at' => now(),
            ]);

            DB::commit();

            return $this->success(
                'Quiz submitted successfully.',
                new QuizAttemptResource(
                    $attempt->fresh()->load([
                        'quiz',
                        'answers.question',
                        'answers.option',
                    ])
                )
            );
        } catch (\Throwable $exception) {
            DB::rollBack();

            return $this->error(
                'Quiz submission failed.',
                config('app.debug')
                    ? ['error' => $exception->getMessage()]
                    : [],
                500
            );
        }
    }

    /**
     * Show a student's quiz result.
     */
    public function result(Quiz $quiz)
    {
        $attempt = QuizAttempt::with([
            'quiz',
            'answers.question',
            'answers.option',
        ])
            ->where('quiz_id', $quiz->id)
            ->where('student_id', Auth::id())
            ->where('status', 'submitted')
            ->latest()
            ->first();

        if (!$attempt) {
            return $this->error(
                'Quiz result not found.',
                [],
                404
            );
        }

        return $this->success(
            'Quiz result retrieved successfully.',
            new QuizAttemptResource($attempt)
        );
    }

    /**
     * Student quiz history.
     */
    public function history()
    {
        $attempts = QuizAttempt::with('quiz.course')
            ->where('student_id', Auth::id())
            ->latest()
            ->paginate(10);

        return $this->success(
            'Quiz history retrieved successfully.',
            QuizAttemptResource::collection($attempts)
        );
    }
}
