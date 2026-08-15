<?php

use App\Features\Notification\Index\IndexNotificationsPage;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::livewire('notifications', IndexNotificationsPage::class)->name('notifications.index');
});
