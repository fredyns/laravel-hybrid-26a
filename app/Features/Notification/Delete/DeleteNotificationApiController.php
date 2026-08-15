<?php

namespace App\Features\Notification\Delete;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteNotificationApiController
{
    public function __invoke(Request $request, string $notificationId, DeleteNotification $action): JsonResponse
    {
        $action->execute($request->user(), $notificationId);

        return response()->json([
            'message' => 'Notification deleted',
        ]);
    }
}
