<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Http\Resources\LessonResource;
use App\Models\Course;
use App\Models\Lesson;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LessonController extends Controller
{
    use ApiResponse;

    /**
     * Display all lessons created by the authenticated teacher.
     */
    public function index()
    {
        $lessons = Lesson::with('course')
            ->whereHas('course', function ($query) {
                $query->where('teacher_id', Auth::id());
            })
            ->orderBy('lesson_order')
            ->paginate(10);

        return $this->success(
            'Lessons retrieved successfully.',
            LessonResource::collection($lessons)
        );
    }

    /**
     * Store a newly created lesson.
     */
    public function store(StoreLessonRequest $request)
    {
        $course = Course::where('id', $request->course_id)
            ->where('teacher_id', Auth::id())
            ->first();

        if (!$course) {
            return $this->error(
                'Course not found or unauthorized.',
                [],
                404
            );
        }

        try {

            DB::beginTransaction();

            $data = $request->validated();

            $data['slug'] = Str::slug($data['title']) . '-' . time();

            $exists = Lesson::where('course_id', $course->id)
                ->where('lesson_order', $data['lesson_order'])
                ->exists();

            if ($exists) {

                DB::rollBack();

                return $this->error(
                    'Lesson order already exists.',
                    [],
                    422
                );
            }

            if (
                $request->video_type === 'upload' &&
                $request->hasFile('video_file')
            ) {

                $data['video_file'] = $request
                    ->file('video_file')
                    ->store('lessons', 'public');
            }

            $lesson = Lesson::create($data);

            DB::commit();

            return $this->success(
                'Lesson created successfully.',
                new LessonResource(
                    $lesson->load('course')
                ),
                201
            );

        } catch (\Throwable $e) {

            DB::rollBack();

            return $this->error(
                'Lesson creation failed.',
                config('app.debug')
                    ? ['error' => $e->getMessage()]
                    : [],
                500
            );
        }
    }

    /**
     * Display a lesson.
     */
    public function show(Lesson $lesson)
    {
        if ($lesson->course->teacher_id != Auth::id()) {

            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        return $this->success(
            'Lesson details.',
            new LessonResource(
                $lesson->load('course')
            )
        );
    }

    /**
     * Update a lesson.
     */
    public function update(UpdateLessonRequest $request, Lesson $lesson)
    {
        if ($lesson->course->teacher_id != Auth::id()) {

            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        try {

            DB::beginTransaction();

            $data = $request->validated();

            if (isset($data['title'])) {
                $data['slug'] = Str::slug($data['title']) . '-' . time();
            }

            if (isset($data['order'])) {

                $exists = Lesson::where('course_id', $lesson->course_id)
                    ->where('lesson_order', $data['lesson_order'])
                    ->where('id', '!=', $lesson->id)
                    ->exists();

                if ($exists) {

                    DB::rollBack();

                    return $this->error(
                        'Lesson order already exists.',
                        [],
                        422
                    );
                }
            }

            if (
                $request->video_type === 'upload' &&
                $request->hasFile('video_file')
            ) {

                if (
                    $lesson->video_file &&
                    Storage::disk('public')->exists($lesson->video_file)
                ) {
                    Storage::disk('public')
                        ->delete($lesson->video_file);
                }

                $data['video_file'] = $request
                    ->file('video_file')
                    ->store('lessons', 'public');

                $data['video_url'] = null;
            }

            if (
                isset($data['video_type']) &&
                in_array($data['video_type'], ['youtube', 'vimeo'])
            ) {

                if (
                    $lesson->video_file &&
                    Storage::disk('public')->exists($lesson->video_file)
                ) {
                    Storage::disk('public')
                        ->delete($lesson->video_file);
                }

                $data['video_file'] = null;
            }

            $lesson->update($data);

            DB::commit();

            return $this->success(
                'Lesson updated successfully.',
                new LessonResource(
                    $lesson->fresh()->load('course')
                )
            );

        } catch (\Throwable $e) {

            DB::rollBack();

            return $this->error(
                'Lesson update failed.',
                config('app.debug')
                    ? ['error' => $e->getMessage()]
                    : [],
                500
            );
        }
    }

    /**
     * Delete a lesson.
     */
    public function destroy(Lesson $lesson)
    {
        if ($lesson->course->teacher_id != Auth::id()) {

            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        try {

            DB::beginTransaction();

            if (
                $lesson->video_file &&
                Storage::disk('public')->exists($lesson->video_file)
            ) {

                Storage::disk('public')
                    ->delete($lesson->video_file);
            }

            $lesson->delete();

            DB::commit();

            return $this->success(
                'Lesson deleted successfully.'
            );

        } catch (\Throwable $e) {

            DB::rollBack();

            return $this->error(
                'Lesson deletion failed.',
                config('app.debug')
                    ? ['error' => $e->getMessage()]
                    : [],
                500
            );
        }
    }

    /**
     * Public lessons for a course.
     */
    public function publicIndex(Course $course)
    {
        $lessons = $course->lessons()
            ->where('status', true)
            ->orderBy('lesson_order')
            ->get();

        return $this->success(
            'Lessons retrieved successfully.',
            LessonResource::collection($lessons)
        );
    }

    /**
     * Public lesson details.
     */
    public function publicShow(Lesson $lesson)
    {
        if (!$lesson->status) {

            return $this->error(
                'Lesson not found.',
                [],
                404
            );
        }

        return $this->success(
            'Lesson details.',
            new LessonResource(
                $lesson->load('course')
            )
        );
    }
}