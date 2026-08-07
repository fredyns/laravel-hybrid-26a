<?php

use App\Features\User\PurgeUnverified\PurgeUnverifiedUsersCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(PurgeUnverifiedUsersCommand::class)->daily();
