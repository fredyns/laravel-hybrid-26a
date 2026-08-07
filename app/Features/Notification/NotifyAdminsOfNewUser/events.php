<?php

use App\Features\Notification\NotifyAdminsOfNewUser\UserCreatedListener;
use App\Features\User\Events\UserCreated;
use Illuminate\Support\Facades\Event;

Event::listen(UserCreated::class, UserCreatedListener::class);
