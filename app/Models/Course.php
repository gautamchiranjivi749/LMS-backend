<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
      protected $fillable = [

        'teacher_id',

        'title',

        'slug',

        'description',

        'thumbnail',

        'price',

        'level',

        'language',

        'status',
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
}
