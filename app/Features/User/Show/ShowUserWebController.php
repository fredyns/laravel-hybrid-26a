<?php

namespace App\Features\User\Show;

use App\Models\User;
use Illuminate\Contracts\View\View;

class ShowUserWebController
{
    public function __invoke(User $user): View
    {
        return view('features::User.Show.view', compact('user'));
    }
}
