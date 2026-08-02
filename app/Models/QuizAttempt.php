<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{


    protected $fillable = [
         'quiz_id',
    'student_id',
    'score',
    'total_marks',
    'percentage',
    'status',
    'started_at',
    'completed_at',
    ];

   protected function casts(): array
{
    return [
        'score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
 

public function quiz()
{
    return $this->belongsTo(Quiz::class);
}

public function student()
{
    return $this->belongsTo(User::class,'student_id');
}

public function answers()
{
    return $this->hasMany(StudentAnswer::class);
}
}
