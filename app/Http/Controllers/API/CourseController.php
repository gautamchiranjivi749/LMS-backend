<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $courses = auth()->user()
            ->courses()
            ->latest()
            ->get();

        return $this->success(
            'Course list',
            CourseResource::collection($courses)
        );
    }
    public function store(StoreCourseRequest  $request)
    {
        $course = Course::create([
            'teacher_id' => auth()->id(),
            'title' => $request->title,
             'slug' => Str::slug($request->title) . '-' . time(),
            'description' => $request->description,
            'price' => $request->price,
            'level' => $request->level,
            'language' => $request->language,
        ]);

        return $this->success(
            'Course created successfully.',
            new CourseResource($course),
            201
        );

    }
      /**
     * Show Course
     */
    public function show(Course $course)
    {
        if ($course->teacher_id != auth()->id()) {
            return $this->error('Unauthorized', [], 403);
        }

        return $this->success(
            'Course details',
            new CourseResource($course)
        );
    }

    /**
     * Update Course
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        if ($course->teacher_id != auth()->id()) {
            return $this->error('Unauthorized', [], 403);
        }

        $course->update($request->validated());

        return $this->success(
            'Course updated.',
            new CourseResource($course)
        );
    }

    /**
     * Delete Course
     */
    public function destroy(Course $course)
    {
        if ($course->teacher_id != auth()->id()) {
            return $this->error('Unauthorized', [], 403);
        }

        $course->delete();

        return $this->success('Course deleted successfully.');
    }

    /**
     * Public Courses
     */
    public function published()
    {
        $courses = Course::with('teacher')
            ->where('status', true)
            ->latest()
            ->get();

        return $this->success(
            'Published courses',
            CourseResource::collection($courses)
        );
    }
}
