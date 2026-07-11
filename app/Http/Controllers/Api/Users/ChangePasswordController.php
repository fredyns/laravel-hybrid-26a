<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Users\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ChangePasswordController extends Controller
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
