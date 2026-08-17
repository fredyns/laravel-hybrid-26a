<?php

namespace App\Features\Settings\DeleteAccount;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DeleteAccountApiController extends Controller
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
