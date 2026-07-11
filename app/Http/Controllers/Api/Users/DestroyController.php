<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DestroyController extends Controller
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
