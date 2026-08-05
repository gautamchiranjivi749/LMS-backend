<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Course;
use App\Http\Resources\LessonResource;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use ApiResponse;

    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }
    public function index(Request $request)
    {
        $lessons =  Lesson::with('[course]')->whereHAs('course',function ($query)
        {
             $query->where('teacher_id', Auth::id());
        })
        ->orderBy('lesson_order')
        ->paginate(10);

    return $this->success(
    'Lesson retrieved successfully.',
    LessonResource::collection($lessons)
);
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(StoreLessonRequest $request)
{
    // Check if the teacher owns the course
    $course = Course::where('id', $request->course_id)
        ->where('teacher_id', auth()->id())
        ->first();

    if (!$course) {
        return response()->json([
            'success' => false,
            'message' => 'Course not found or you are not authorized.'
        ], 404);
    }

    try {

        DB::beginTransaction();

        $data = $request->validated();

        // Generate unique slug
        $data['slug'] = Str::slug($data['title']) . '-' . time();

        // Prevent duplicate lesson order in same course
        $exists = Lesson::where('course_id', $course->id)
            ->where('order', $data['order'])
            ->exists();

        if ($exists) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Lesson order already exists for this course.'
            ], 422);
        }

        // Upload video if required
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

        return response()->json([
            'success' => true,
            'message' => 'Lesson created successfully.',
            'data' => new LessonResource(
                $lesson->load('course')
            )
        ], 201);

    } catch (\Throwable $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Lesson creation failed.',
            'error' => config('app.debug')
                ? $e->getMessage()
                : null,
        ], 500);
    }
}


    /**
     * Display the specified resource.
     */
    public function show(Lesson $lesson)
{
    if ($lesson->course->teacher_id != Auth::id()) {
        return $this->errorResponse('Unauthorized.', 403);
    }

    return $this->successResponse(
        new LessonResource($lesson->load('course')),
        'Lesson details.'
    );
}

//GET /api/courses/{course}/lessons

public function publicIndex(Course $course)
{
    $lessons = $course->lessons()
        ->where('status', true)
        ->orderBy('lesson_order')
        ->get();

    return $this->successResponse(
        LessonResource::collection($lessons),
        'Lessons retrieved successfully.'
    );
}

//GET /api/lessons/{lesson}

public function publicShow(Lesson $lesson)
{
    if (!$lesson->status) {
        return $this->errorResponse(
            'Lesson not found.',
            404
        );
    }

    return $this->successResponse(
        new LessonResource($lesson->load('course')),
        'Lesson details.'
    );
}
    /**
     * Update the specified resource in storage.
     */
  public function update(UpdateLessonRequest $request, Lesson $lesson)
{
    // Check ownership
    if ($lesson->course->teacher_id != auth()->id()) {

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized.'
        ], 403);
    }

    try {

        DB::beginTransaction();

        $data = $request->validated();

        // Update slug if title changes
        if (isset($data['title'])) {

            $data['slug'] = Str::slug($data['title']) . '-' . time();
        }

        // Prevent duplicate lesson order
        if (isset($data['order'])) {

            $exists = Lesson::where('course_id', $lesson->course_id)
                ->where('order', $data['order'])
                ->where('id', '!=', $lesson->id)
                ->exists();

            if ($exists) {

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Lesson order already exists.'
                ], 422);
            }
        }

        // Upload new video
        if (
            $request->video_type == 'upload' &&
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

        // If switching to YouTube/Vimeo
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

        return response()->json([
            'success' => true,
            'message' => 'Lesson updated successfully.',
            'data' => new LessonResource(
                $lesson->fresh()->load('course')
            )
        ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Lesson update failed.',
            'error' => config('app.debug')
                ? $e->getMessage()
                : null
        ], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */
 public function destroy(Lesson $lesson)
{
    // Check ownership
    if ($lesson->course->teacher_id != auth()->id()) {

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized.'
        ], 403);
    }

    try {

        DB::beginTransaction();

        // Delete uploaded video if exists
        if (
            $lesson->video_file &&
            Storage::disk('public')->exists($lesson->video_file)
        ) {

            Storage::disk('public')->delete($lesson->video_file);
        }

        // Soft delete
        $lesson->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Lesson deleted successfully.'
        ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Lesson deletion failed.',
            'error' => config('app.debug')
                ? $e->getMessage()
                : null
        ], 500);
    }
}
}
