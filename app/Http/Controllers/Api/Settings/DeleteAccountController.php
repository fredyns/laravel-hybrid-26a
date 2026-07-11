<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Settings\DeleteAccountRequest;
use Illuminate\Http\JsonResponse;

class DeleteAccountController extends Controller
{
    /**
     * Delete the authenticated user's own account and revoke all tokens.
     */
    public function __invoke(DeleteAccountRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->tokens()->delete();
        $user->delete(); // todo: deactivate instead of delete

        return response()->json([
            'message' => 'Account deleted',
        ]);
    }
}
