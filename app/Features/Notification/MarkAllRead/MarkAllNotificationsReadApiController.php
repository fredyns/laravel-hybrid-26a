<?php

namespace App\Features\Notification\MarkAllRead;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarkAllNotificationsReadApiController
{
    public function __invoke(Request $request, MarkAllNotificationsRead $action): JsonResponse
    {
        $action->execute($request->user());

        return response()->json([
            'message' => 'All notifications marked as read',
        ]);
    }
}
