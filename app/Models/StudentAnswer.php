<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
 
   protected $fillable = [
    'quiz_attempt_id',
    'question_id',
    'question_option_id',
    'answer_text',
    'is_correct',
    'marks_obtained',
];

    public function casts(): array
    {
        return [
            'answer_text' => 'string',
        ];
    }

public function attempt()
{
    return $this->belongsTo(QuizAttempt::class);
}

public function question()
{
    return $this->belongsTo(Question::class);
}

public function option()
{
    return $this->belongsTo(
        QuestionOption::class,
        'question_option_id'
    );
}
}
