<?php

namespace App\Features\User\Update;

use App\Features\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UpdateUserApiController
{
    public function __invoke(UpdateUserRequest $request, User $user, UpdateUser $action): JsonResponse
    {
        /** @var array{name: string, email: string} $validated */
        $validated = $request->validated();

        $user = $action->execute($user, $validated);

        return response()->json([
            'message' => 'User updated',
            'user' => UserResource::make($user),
        ]);
    }
}
