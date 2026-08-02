<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Course;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function courses()
    {
        return $this->hasMany(Course::class,'teacher_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class,);
    }
    /**
 * Courses enrolled by the student
 */
    public function enrolledCourses()
    {
        return $this->belongsToMany(
            Course::class,
            'enrollments'
        )
        ->withPivot([
            'status',
            'progress',
            'enrolled_at',
        ])
        ->withTimestamps();
    }
    public function quizAttempts()
    {
        return $this->hasMany(
            QuizAttempt::class,
            'student_id'
        );
    }

}
