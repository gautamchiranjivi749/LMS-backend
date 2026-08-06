<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWishlistRequest;
use App\Http\Resources\WishlistResource;
use App\Models\Course;
use App\Models\Wishlist;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    use ApiResponse;
    

    /**
     * My Wishlist
     */
    public function index()
    {
        $wishlists = Wishlist::with('course')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return $this->success(
            'Wishlist retrieved successfully.',
            WishlistResource::collection($wishlists)
        );
    }

    /**
     * Add Course
     */
    public function store(StoreWishlistRequest $request)
    {
        $course = Course::where('id', $request->course_id)
            ->where('status', true)
            ->firstOrFail();

        $exists = Wishlist::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->exists();

        if ($exists) {

            return $this->error(
                'Course already exists in wishlist.',
                [],
                409
            );
        }

        $wishlist = Wishlist::create([

            'user_id' => Auth::id(),

            'course_id' => $course->id,

        ]);

        return $this->success(
            'Course added to wishlist.',
            new WishlistResource(
                $wishlist->load('course')
            ),
            201
        );
    }

    /**
     * Remove Course
     */
    public function destroy(Wishlist $wishlist)
    {
        if ($wishlist->user_id != Auth::id()) {

            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        $wishlist->delete();

        return $this->success(
            'Course removed from wishlist.'
        );
    }

    /**
     * Check Wishlist
     */
    public function check(Course $course)
    {
        $exists = Wishlist::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->exists();

        return $this->success(
            'Wishlist status.',
            [
                'in_wishlist' => $exists
            ]
        );
    }
}
