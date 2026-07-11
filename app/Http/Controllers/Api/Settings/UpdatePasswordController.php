<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Settings\UpdatePasswordRequest;
use Illuminate\Http\JsonResponse;

class UpdatePasswordController extends Controller
{
    /**
     * Update the authenticated user's own password.
     */
    public function __invoke(UpdatePasswordRequest $request): JsonResponse
    {
        $request->user()->update([
            'password' => $request->validated('password'),
        ]);

        return response()->json([
            'message' => 'Password updated',
        ]);
    }
}
