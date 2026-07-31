<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
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
            'student'=>[
                'id'=>$this->user->id,
                'name'=>$this->user->name,
            ],
             'course' => [
                'id' => $this->course->id,
                'title' => $this->course->title,
                'price' => $this->course->price,
            ],

            'status' => $this->status,

            'progress' => $this->progress,

            'enrolled_at' => $this->enrolled_at,
        ];
    }
}
