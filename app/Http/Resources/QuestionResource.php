<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
              'id'=>$this->id,

        'quiz'=>[
            'id'=>$this->quiz->id,
            'title'=>$this->quiz->title,
        ],

        'question'=>$this->question,

        'question_type'=>$this->question_type,

        'marks'=>$this->marks,

        'question_order'=>$this->question_order,

        'status'=>$this->status,

        'created_at'=>$this->created_at,

        ];
    }
}
