<?php

namespace App\Features\Notification\MarkRead;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarkNotificationReadApiController
{
    public function __invoke(Request $request, string $notificationId, MarkNotificationRead $action): JsonResponse
    {
        $action->execute($request->user(), $notificationId);

        return response()->json([
            'message' => 'Notification marked as read',
        ]);
    }
}
