<?php

namespace App\Features\Auth\Login;

use App\Features\User\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginApiController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        if (! Auth::attempt($request->validated())) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => UserResource::make($user),
            'token' => $token,
        ]);
    }
}
