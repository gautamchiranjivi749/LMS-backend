<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
   protected $fillable = [
    'teacher_id',
    'category_id',
    'title',
    'slug',
    'description',
    'thumbnail',
    'price',
    'level',
    'language',
    'status',
    'published_at',
];
     protected function casts(): array
    {
        return [

            'status'=>'boolean',

            'price'=>'decimal:2',
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(User::class,'teacher_id');
    }
    public function skills()
    {
        return $this->belongsToMany(
            Skill::class,
            'course_skill'
        );
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function lessons()
    {
        return $this->hasMany(Lesson::class)
                    ->orderBy('lesson_order');
    }
            /**
         * Course enrollments
         */
        public function enrollments()
        {
            return $this->hasMany(Enrollment::class);
        }

        /**
        * Students enrolled in this course
        */
        public function students()
        {
            return $this->belongsToMany(
                User::class,
                'enrollments'
            )
            ->withPivot([
                'status',
                'progress',
                'enrolled_at',
            ])
            ->withTimestamps();
        }

        public function quizzes()
        {
            return $this->hasMany(Quiz::class);
        }   

        public function certificates()
        {
            return $this->hasMany(Certificate::class);
        }

      
}
