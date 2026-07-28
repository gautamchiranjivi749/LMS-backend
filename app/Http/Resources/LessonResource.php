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
            'id'=>$this->id,
            'course_id'=>$this->course_id,
            'course'=>$this->whenLoaded('course'),
              'title'=>$this->title,

        'description'=>$this->description,

        'video'=>$this->video
            ? asset('storage/'.$this->video)
            : null,

        'duration'=>$this->duration,

        'lesson_order'=>$this->lesson_order,

        'is_preview'=>$this->is_preview,

        'status'=>$this->status,

        'created_at'=>$this->created_at,

        ];
    }
}
