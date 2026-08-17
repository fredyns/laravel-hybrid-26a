<?php

namespace App\Features\Settings\UpdatePassword;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class UpdatePasswordApiController extends Controller
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
