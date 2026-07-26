<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class UserIndexController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Request $request)
    {
        $this->authorize('users.index');

        $search = $request->query('q', '');
        $sortColumn = $request->query('sort', 'created_at');
        $sortDirection = $request->query('direction', 'desc');
        $perPage = (int) $request->query('per_page', 10);
        $visibleColumns = $request->query('columns') ? explode(',', $request->query('columns')) : ['name', 'email', 'created_at'];

        $users = User::query()
            ->when($search !== '', fn ($query) => $query->search($search))
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage)
            ->appends($request->query());

        $columns = [
            'name' => __('Name'),
            'email' => __('Email'),
            'email_verified_at' => __('Verified'),
            'created_at' => __('Created'),
            'updated_at' => __('Updated'),
        ];

        return view('users.index', compact(
            'users',
            'columns',
            'search',
            'sortColumn',
            'sortDirection',
            'perPage',
            'visibleColumns'
        ));
    }
}
