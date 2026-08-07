<?php

namespace App\Features\User\Show;

use App\Features\User\UserResource;
use App\Models\User;

class ShowUserApiController
{
    public function __invoke(User $user): UserResource
    {
        return UserResource::make($user);
    }
}
