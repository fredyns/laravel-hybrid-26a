<?php

namespace App\Features\User\ChangePassword;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class ChangePasswordApiController
{
    public function __invoke(ChangePasswordRequest $request, User $user): JsonResponse
    {
        $user->update([
            'password' => $request->validated('password'),
        ]);

        return response()->json([
            'message' => 'Password updated',
        ]);
    }
}
