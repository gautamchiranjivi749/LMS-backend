<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
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

        'course' => [
            'id' => $this->course->id,
            'title' => $this->course->title,
        ],

        'title' => $this->title,

        'description' => $this->description,

        'time_limit' => $this->time_limit,

        'passing_marks' => $this->passing_marks,

        'total_marks' => $this->total_marks,

        'status' => $this->status,

        'created_at' => $this->created_at,
        ];
    }
}
