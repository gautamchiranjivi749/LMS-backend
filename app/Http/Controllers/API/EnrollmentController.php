<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Resources\EnrollmentResource;
use App\Models\Enrollment;
use App\Models\Course;
use App\Traits\ApiResponse;
use App\Models\Notification;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $enrollments = Enrollment::with(['course'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return $this->success(
            'My enrolled courses.',
            EnrollmentResource::collection($enrollments)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
       public function store(StoreEnrollmentRequest $request)
    {
        $course = Course::findOrFail($request->course_id);

        // Prevent duplicate enrollment
        $exists = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->exists();

        if ($exists) {
            return $this->error(
                'You are already enrolled in this course.',
                [],
                409
            );
        }

      DB::beginTransaction();

try {

    $enrollment = Enrollment::create([
        'user_id' => Auth::id(),
        'course_id' => $course->id,
        'status' => 'active',
        'progress' => 0,
        'enrolled_at' => now(),
    ]);
    ActivityLogger::log(

    'Enroll',

    'Course',

    $course->id,

    'Student enrolled in '.$course->title

);

    Notification::create([
        'user_id' => Auth::id(),
        'title' => 'Course Enrolled',
        'message' => 'You have successfully enrolled in '.$course->title,
        'type' => 'enrollment',
    ]);

    DB::commit();

    return $this->success(
        'Enrollment successful.',
        new EnrollmentResource(
            $enrollment->load(['user','course'])
        ),
        201
    );

    } catch (\Throwable $e) {

        DB::rollBack();

        return $this->error(
            'Enrollment failed.',
            config('app.debug')
                ? ['error' => $e->getMessage()]
                : [],
            500
        );
    }
    }
    /**
     * Display the specified resource.
     */
     public function show(Course $course)
    {
        $enrollment = Enrollment::with(['user', 'course'])
            ->where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return $this->error(
                'Enrollment not found.',
                [],
                404
            );
        }

        return $this->success(
            'Enrollment details.',
            new EnrollmentResource($enrollment)
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Enrollment $enrollment)
{
    // Student can only update their own enrollment
    if ($enrollment->user_id != Auth::id()) {
        return $this->error(
            'Unauthorized.',
            [],
            403
        );
    }

    $validated = $request->validate([
        'progress' => 'nullable|integer|min:0|max:100',
        'status' => 'nullable|in:active,completed,cancelled',
    ]);

    $enrollment->update($validated);

    return $this->success(
        'Enrollment updated successfully.',
        new EnrollmentResource(
            $enrollment->fresh()->load(['user', 'course'])
        )
    );
}

    /**
     * Remove the specified resource from storage.
     */
     public function destroy(Course $course)
    {
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return $this->error(
                'Enrollment not found.',
                [],
                404
            );
        }

        $enrollment->delete();

        return $this->success(
            'Enrollment cancelled successfully.'
        );
    }
}
