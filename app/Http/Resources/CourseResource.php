<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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

    'teacher' => [
        'id' => $this->teacher->id,
        'name' => $this->teacher->name,
    ],

    'category' => $this->category ? [
        'id' => $this->category->id,
        'name' => $this->category->name,
    ] : null,

    'skills' => $this->skills->map(function ($skill) {
        return [
            'id' => $skill->id,
            'name' => $skill->name,
        ];
    }),

    'title' => $this->title,
    'slug' => $this->slug,
    'description' => $this->description,
    'thumbnail' => $this->thumbnail,
    'price' => $this->price,
    'level' => $this->level,
    'language' => $this->language,
    'status' => $this->status,
    'published_at' => $this->published_at,
    'created_at' => $this->created_at,
];
    }
}
