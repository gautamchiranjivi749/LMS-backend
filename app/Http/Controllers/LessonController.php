<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Course;
use App\Http\Resources\LessonResource;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponse;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use apiResponse;

    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }
    public function index(Request $request)
    {
        $lessons =  Lesson::with('course')->whereHAs('course',function ($query)
        {
             $query->where('teacher_id', Auth::id());
        })
          ->latest()
        ->paginate(10);

    return $this->successResponse(
        LessonResource::collection($lessons),
        'Lessons retrieved successfully.'
    );
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(StoreLessonRequest $request)
{
    $course = Course::where('id', $request->course_id)
        ->where('teacher_id', Auth::id())
        ->first();

    if (!$course) {
        return $this->errorResponse('Unauthorized course.', 403);
    }

    $data = $request->validated();

    if ($request->hasFile('video')) {
        $data['video'] = $this->fileUploadService
            ->upload($request->file('video'), 'lessons');
    }

    $lesson = Lesson::create($data);

    return $this->successResponse(
        new LessonResource($lesson->load('course')),
        'Lesson created successfully.',
        201
    );
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
    /**
     * Update the specified resource in storage.
     */
   public function update(UpdateLessonRequest $request, Lesson $lesson)
{
    if ($lesson->course->teacher_id != Auth::id()) {
        return $this->errorResponse('Unauthorized.', 403);
    }

    $data = $request->validated();

    if ($request->hasFile('video')) {

        if ($lesson->video) {
            $this->fileUploadService->delete($lesson->video);
        }

        $data['video'] = $this->fileUploadService
            ->upload($request->file('video'), 'lessons');
    }

    $lesson->update($data);

    return $this->successResponse(
        new LessonResource($lesson->fresh()->load('course')),
        'Lesson updated successfully.'
    );
}

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Lesson $lesson)
{
    if ($lesson->course->teacher_id != Auth::id()) {
        return $this->errorResponse('Unauthorized.', 403);
    }

    if ($lesson->video) {
        $this->fileUploadService->delete($lesson->video);
    }

    $lesson->delete();

    return $this->successResponse(
        null,
        'Lesson deleted successfully.'
    );
}
}
