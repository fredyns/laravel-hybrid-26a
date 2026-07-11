<?php

namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarkReadController extends Controller
{
    public function __invoke(Request $request, string $notificationId): JsonResponse
    {
        $notification = $request->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
        ]);
    }
}
