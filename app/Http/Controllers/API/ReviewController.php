<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use App\Traits\ApiResponse;
use App\Models\Notification;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    use ApiResponse;

    /**
     * Display reviews for a course.
     */
    public function index(Course $course)
    {
        $reviews = Review::with('user')
            ->where('course_id', $course->id)
            ->where('status', true)
            ->latest()
            ->paginate(10);

        return $this->success(
            'Reviews retrieved successfully.',
            [
                'average_rating' => round($course->reviews()->avg('rating'), 1),
                'total_reviews' => $course->reviews()->count(),
                'reviews' => ReviewResource::collection($reviews),
            ]
        );
    }
   

    /**
     * Store a new review.
     */
    public function store(StoreReviewRequest $request, Course $course)
    {
        $studentId = Auth::id();

        $enrolled = Enrollment::where('user_id', $studentId)
            ->where('course_id', $course->id)
            ->exists();

        if (!$enrolled) {
            return $this->error(
                'You must enroll before reviewing this course.',
                [],
                403
            );
        }

        if (
            Review::where('user_id', $studentId)
                ->where('course_id', $course->id)
                ->exists()
        ) {
            return $this->error(
                'You have already reviewed this course.',
                [],
                422
            );
        }

        try {

            DB::beginTransaction();

            $review = Review::create([
                'course_id' => $course->id,
                'user_id' => $studentId,
                'rating' => $request->rating,
                'review' => $request->review,
                'status' => true,
            ]);
            ActivityLogger::log(

            'Create',

            'Review',

            $review->id,

            'Review submitted.'

        );

             Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Review Submitted',
            'message' => 'Thank you for reviewing this course.',
            'type' => 'review',
        ]);

            DB::commit();
            
            

            return $this->success(
                'Review submitted successfully.',
                new ReviewResource(
                    $review->load(['user', 'course'])
                ),
                201
            );

        } catch (\Throwable $e) {

            DB::rollBack();

            return $this->error(
                'Review submission failed.',
                config('app.debug')
                    ? ['error' => $e->getMessage()]
                    : [],
                500
            );
        }
    }

    /**
     * Show a review.
     */
    public function show(Review $review)
    {
        return $this->success(
            'Review details.',
            new ReviewResource(
                $review->load(['user', 'course'])
            )
        );
    }

    /**
     * Update review.
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        if ($review->user_id != Auth::id()) {

            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        $review->update($request->validated());

        return $this->success(
            'Review updated successfully.',
            new ReviewResource(
                $review->fresh()->load(['user', 'course'])
            )
        );
    }

    /**
     * Delete review.
     */
    public function destroy(Review $review)
    {
        if ($review->user_id != Auth::id()) {

            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        $review->delete();

        return $this->success(
            'Review deleted successfully.'
        );
    }
}