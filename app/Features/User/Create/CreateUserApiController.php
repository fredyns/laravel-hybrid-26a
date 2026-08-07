<?php

namespace App\Features\User\Create;

use App\Features\User\UserResource;
use Illuminate\Http\JsonResponse;

class CreateUserApiController
{
    public function __invoke(CreateUserRequest $request, CreateUser $action): JsonResponse
    {
        /** @var array{name: string, email: string, password: string} $validated */
        $validated = $request->validated();

        $user = $action->execute($validated);

        return response()->json([
            'message' => 'User created',
            'user' => UserResource::make($user),
        ], 201);
    }
}
