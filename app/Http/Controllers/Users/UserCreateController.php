<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserCreateController extends Controller
{
    use AuthorizesRequests;

    public function __invoke()
    {
        $this->authorize('users.create');

        return view('users.create');
    }
}
