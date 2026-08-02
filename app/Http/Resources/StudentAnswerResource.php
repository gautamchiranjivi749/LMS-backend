<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentAnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return   
        [
            'id' => $this->id,

            'question' => [
                'id' => $this->question->id,
                'question' => $this->question->question,
                'marks' => $this->question->marks,
            ],

            'selected_option' => $this->option
                ? [
                    'id' => $this->option->id,
                    'option_text' => $this->option->option_text,
                ]
                : null,

            'answer_text' => $this->answer_text,
            'is_correct' => $this->is_correct,
            'marks_obtained' => $this->marks_obtained,
        ];
}
}