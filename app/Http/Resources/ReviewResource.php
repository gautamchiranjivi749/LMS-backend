<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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

            'student' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],

            'rating' => $this->rating,

            'review' => $this->review,

            'status' => $this->status,

            'created_at' => $this->created_at,

        ];
    }
}
