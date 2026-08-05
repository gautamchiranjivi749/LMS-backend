<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
class Review extends Model
{
   
    protected $fillable = [
        'course_id',

        'user_id',

        'rating',

        'review',

        'status',
    ];
     protected function casts(): array
    {
        return [

            'rating' => 'integer',

            'status' => 'boolean',

        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
