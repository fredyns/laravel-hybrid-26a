<?php

namespace App\Features\Settings\UpdateProfile;

use App\Features\User\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class UpdateProfileApiController extends Controller
{
    /**
     * Update the authenticated user's own name and email.
     */
    public function __invoke(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated',
            'user' => UserResource::make($user),
        ]);
    }
}
