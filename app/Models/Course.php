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
}
