<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Settings\UpdateProfileRequest;
use App\Features\User\UserResource;
use Illuminate\Http\JsonResponse;

class UpdateProfileController extends Controller
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
