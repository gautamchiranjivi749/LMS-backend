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
    'teacher_id'   => auth()->id(),
    'category_id'  => $request->category_id,
    'title'        => $request->title,
    'slug'         => Str::slug($request->title).'-'.time(),
    'description'  => $request->description,
    'thumbnail'    => $thumbnail ?? null,
    'price'        => $request->price,
    'level'        => $request->level,
    'language'     => $request->language,
    'status'       => $request->boolean('status'),
    'published_at' => $request->boolean('status') ? now() : null,
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
    public function publicIndex(Request $request)
{
    $query = Course::with(['teacher', 'category', 'skills']);
        // ->where('status', true);

    // Search
    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }

    // Category Filter
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // Latest first
    $courses = $query->latest()->paginate(10);

    return $this->success(
        'Published courses retrieved successfully.',
        CourseResource::collection($courses)
    );
}
public function publicShow(Course $course)
{
    if (!$course->status) {
        return $this->error(
            'Course not found.',
            [],
            404
        );
    }

    $course->load([
        'teacher',
        'category',
        'skills'
    ]);

    return $this->success(
        'Course details.',
        new CourseResource($course)
    );
}
public function latestCourses()
{
    $courses = Course::with('teacher')
        ->where('status', true)
        ->latest()
        ->take(6)
        ->get();

    return $this->success(
        'Latest courses.',
        CourseResource::collection($courses)
    );
}
public function categoryCourses($categoryId)
{
    $courses = Course::with('teacher')
        ->where('category_id', $categoryId)
        ->where('status', true)
        ->latest()
        ->get();

    return $this->success(
        'Category courses.',
        CourseResource::collection($courses)
    );
}
public function skillCourses($skillId)
{
    $courses = Course::with('teacher')
        ->whereHas('skills', function ($query) use ($skillId) {
            $query->where('skills.id', $skillId);
        })
        ->where('status', true)
        ->latest()
        ->get();

    return $this->success(
        'Skill courses.',
        CourseResource::collection($courses)
    );
}
}
