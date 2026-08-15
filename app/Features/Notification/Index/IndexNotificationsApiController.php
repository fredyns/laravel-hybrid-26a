<?php

namespace App\Features\Notification\Index;

use App\Features\Notification\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexNotificationsApiController
{
    /**
     * The authenticated user's notifications, newest first.
     */
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate($validated['per_page'] ?? 15);

        return NotificationResource::collection($notifications)->additional([
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }
}
