<?php

namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DestroyController extends Controller
{
    public function __invoke(Request $request, string $notificationId): JsonResponse
    {
        $request->user()->notifications()->findOrFail($notificationId)->delete();

        return response()->json([
            'message' => 'Notification deleted',
        ]);
    }
}
