<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;

class ShowController extends Controller
{
    public function __invoke(User $user): UserResource
    {
        return UserResource::make($user);
    }
}
