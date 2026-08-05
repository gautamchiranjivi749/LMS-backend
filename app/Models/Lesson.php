<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use softDeletes;
    protected $fillable = [
         'course_id',
    'title',
    'slug',
    'description',
    'video_type',
    'video_url',
    'video_file',
    'duration',
    'lesson_order',
    'is_preview',
    'status',
    ];


    protected function casts(): array
    {
        return [
             'duration' => 'integer',
        'lesson_order' => 'integer',
        'is_preview' => 'boolean',
        'status' => 'boolean',
        ];
    }


    public function course()
{
    return $this->belongsTo(Course::class);
}

public function progress()
{
    return $this->hasMany(LessonProgress::class);
}
   
}

