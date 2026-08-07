<?php

namespace App\Features\User\Delete;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteUserApiController
{
    public function __invoke(Request $request, User $user): JsonResponse
    {
        if ((string) $request->user()->getKey() === (string) $user->getKey()) {
            return response()->json([
                'message' => 'You cannot delete your own account.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted',
        ]);
    }
}
