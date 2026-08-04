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


            'duration' => $this->duration,
            'video_type' => $this->video_type,

            'video_url' => $this->video_url,

            'video_file' => $this->video_file,

            'order' => $this->order,

            'is_preview' => $this->is_preview,

            'status' => $this->status,

            'created_at' => $this->created_at,
        ];
    }
}
