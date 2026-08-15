<?php

use App\Features\Notification\Delete\DeleteNotificationApiController;
use App\Features\Notification\Index\IndexNotificationsApiController;
use App\Features\Notification\MarkAllRead\MarkAllNotificationsReadApiController;
use App\Features\Notification\MarkRead\MarkNotificationReadApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('notifications')->group(function (): void {
    Route::get('/', IndexNotificationsApiController::class)->name('api.notifications.index');
    Route::post('read-all', MarkAllNotificationsReadApiController::class)->name('api.notifications.read-all');
    Route::put('{notificationId}/read', MarkNotificationReadApiController::class)->name('api.notifications.read');
    Route::delete('{notificationId}', DeleteNotificationApiController::class)->name('api.notifications.destroy');
});
