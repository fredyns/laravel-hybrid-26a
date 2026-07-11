<?php

namespace App\Http\Requests\Api\Roles;

use App\Enums\AuthGuard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rbac_roles', 'name')->where('guard_name', $this->input('guard')),
            ],
            'guard' => ['required', 'string', AuthGuard::rule()],
            'permissions' => ['array'],
            'permissions.*' => [
                'string',
                Rule::exists('rbac_permissions', 'name')->where('guard_name', $this->input('guard')),
            ],
        ];
    }
}
