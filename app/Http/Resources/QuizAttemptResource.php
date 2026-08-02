<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizAttemptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
          $passed = $this->status === 'submitted'
            && $this->score >= $this->quiz->passing_marks;

        return 
        [
            'id' => $this->id,

            'quiz' => [
                'id' => $this->quiz->id,
                'title' => $this->quiz->title,
                'passing_marks' => $this->quiz->passing_marks,
            ],

            'score' => $this->score,
            'total_marks' => $this->total_marks,
            'percentage' => $this->percentage,
            'result' => $this->status === 'submitted'
                ? ($passed ? 'passed' : 'failed')
                : null,

            'status' => $this->status,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,

            'answers' => StudentAnswerResource::collection(
                $this->whenLoaded('answers')
            ),
        ];
}
}