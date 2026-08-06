<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * Display all notifications for logged in user.
     */
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return $this->success(
            'Notifications retrieved successfully.',
            [
                'unread_count' => Notification::where(
                    'user_id',
                    Auth::id()
                )->where('is_read', false)->count(),

                'notifications' => NotificationResource::collection(
                    $notifications
                ),
            ]
        );
    }

    /**
     * Display a specific notification.
     */
    public function show(Notification $notification)
    {
        if ($notification->user_id != Auth::id()) {

            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        return $this->success(
            'Notification details.',
            new NotificationResource($notification)
        );
    }

    /**
     * Mark notification as read.
     */
    public function update(Notification $notification)
    {
        if ($notification->user_id != Auth::id()) {

            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        $notification->update([
            'is_read' => true,
        ]);

        return $this->success(
            'Notification marked as read.',
            new NotificationResource($notification->fresh())
        );
    }

    /**
     * Delete notification.
     */
    public function destroy(Notification $notification)
    {
        if ($notification->user_id != Auth::id()) {

            return $this->error(
                'Unauthorized.',
                [],
                403
            );
        }

        $notification->delete();

        return $this->success(
            'Notification deleted successfully.'
        );
    }
}