<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [

    'user_id',
    'course_id',
    'enrolled_at',
    'status',
    'progress',
    ];

      protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'progress' => 'integer',
        ];
    }

    /**
     * Student
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Course
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
