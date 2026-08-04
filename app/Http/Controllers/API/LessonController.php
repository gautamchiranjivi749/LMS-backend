<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $lessons = Lesson::with('course')
        ->whereHas('course', function ($query) {
            $query->where('teacher_id', auth()->id());
        })
        ->latest()
        ->paginate(10);

    return LessonResource::collection($lessons);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLessonRequest $request)
{
    $course = Course::where('id', $request->course_id)
        ->where('teacher_id', auth()->id())
        ->firstOrFail();

    $data = $request->validated();

    $data['slug'] = Str::slug($data['title']) . '-' . time();

    if ($request->hasFile('video_file')) {

        $data['video_file'] = $request
            ->file('video_file')
            ->store('lessons', 'public');

    }

    $lesson = Lesson::create($data);

    return response()->json([
        'success' => true,
        'message' => 'Lesson created successfully.',
        'data' => new LessonResource(
            $lesson->load('course')
        )
    ], 201);
}

    /**
     * Display the specified resource.
     */
   public function show(Lesson $lesson)
{
    return new LessonResource(
        $lesson->load('course')
    );
}

    /**
     * Update the specified resource in storage.
     */
   public function update(UpdateLessonRequest $request, Lesson $lesson)
{
    abort_if(
        $lesson->course->teacher_id != auth()->id(),
        403
    );

    $data = $request->validated();

    if ($request->hasFile('video_file')) {

        if ($lesson->video_file &&
            Storage::disk('public')->exists($lesson->video_file)) {

            Storage::disk('public')
                ->delete($lesson->video_file);

        }

        $data['video_file'] = $request
            ->file('video_file')
            ->store('lessons', 'public');
    }

    $lesson->update($data);

    return response()->json([
        'success' => true,
        'message' => 'Lesson updated successfully.',
        'data' => new LessonResource(
            $lesson->fresh()->load('course')
        )
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Lesson $lesson)
{
    abort_if(
        $lesson->course->teacher_id != auth()->id(),
        403
    );

    if ($lesson->video_file &&
        Storage::disk('public')->exists($lesson->video_file)) {

        Storage::disk('public')
            ->delete($lesson->video_file);

    }

    $lesson->delete();

    return response()->json([
        'success' => true,
        'message' => 'Lesson deleted successfully.'
    ]);
}
}
