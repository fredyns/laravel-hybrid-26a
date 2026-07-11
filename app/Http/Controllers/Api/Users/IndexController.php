<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class IndexController extends Controller
{
    /**
     * List users with optional search, sorting and pagination.
     */
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'sort_by' => ['nullable', Rule::in(['name', 'email', 'email_verified_at', 'created_at', 'updated_at'])],
            'sort_dir' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $users = User::query()
            ->when(($validated['search'] ?? '') !== '', fn ($query) => $query->search($validated['search']))
            ->orderBy($validated['sort_by'] ?? 'created_at', $validated['sort_dir'] ?? 'desc')
            ->paginate($validated['per_page'] ?? 10);

        return UserResource::collection($users);
    }
}
