<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'video',
        'duration',
        'lesson_order',
        'is_preview',
        'status',
    ];


    protected function casts(): array
    {
        return [
            'is_preview' => 'boolean',
            'status' => 'boolean',
            'duration' => 'integer',
            'lesson_order' => 'integer',
        ];
    }


    public function course()
{
    return $this->belongsTo(Course::class);
}

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
}

