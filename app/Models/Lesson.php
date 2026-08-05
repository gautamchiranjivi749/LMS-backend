<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    'order',
    'is_preview',
    'status',
    ];


    protected function casts(): array
    {
        return [
             'duration' => 'integer',
        'order' => 'integer',
        'is_preview' => 'boolean',
        'status' => 'boolean',
        ];
    }


    public function course()
{
    return $this->belongsTo(Course::class);
}

   
}

