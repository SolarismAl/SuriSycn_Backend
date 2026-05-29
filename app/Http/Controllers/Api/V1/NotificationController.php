<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseApiController;
use Illuminate\Http\Request;

class NotificationController extends BaseApiController
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(15);
        return $this->successResponse($notifications, 'Notifications retrieved successfully');
    }

    public function unread(Request $request)
    {
        $notifications = $request->user()->unreadNotifications()->get();
        return $this->successResponse($notifications, 'Unread notifications retrieved successfully');
    }

    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return $this->successResponse(null, 'Notification marked as read');
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return $this->successResponse(null, 'All notifications marked as read');
    }
}
