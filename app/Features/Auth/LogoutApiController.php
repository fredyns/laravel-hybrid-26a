<?php

namespace App\Features\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutApiController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }
}
