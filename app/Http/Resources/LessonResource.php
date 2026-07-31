<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
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

            'video' => $this->video,

            'duration' => $this->duration,

            'lesson_order' => $this->lesson_order,

            'is_preview' => $this->is_preview,

            'status' => $this->status,

            'created_at' => $this->created_at,
        ];
    }
}
