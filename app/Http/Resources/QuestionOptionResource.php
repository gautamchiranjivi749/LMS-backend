<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionOptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [  
        'id' => $this->id,

        'question' => [
            'id' => $this->question->id,
            'question' => $this->question->question,
        ],

        'option_text' => $this->option_text,

        'is_correct' => $this->is_correct,

        'option_order' => $this->option_order,

        'created_at' => $this->created_at,
    ];
    }
}
