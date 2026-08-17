<?php

namespace App\Features\Auth\Register;

use App\Features\User\UserResource;
use App\Http\Controllers\Controller;
use App\Models\User;

class RegisterApiController extends Controller
{
    public function __invoke(RegisterRequest $request)
    {
        $user = User::create($request->validated());
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => UserResource::make($user),
            'token' => $token,
        ], 201);
    }
}
