<?php

namespace App\Features\Rbac\Roles\Create;

use App\Enums\AuthGuard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return self::rulesFor($this->input('guard'));
    }

    /**
     * Single source of validation rules for the Create slice,
     * shared by this request and the Livewire page.
     *
     * @return array<string, mixed>
     */
    public static function rulesFor(?string $guard): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rbac_roles', 'name')->where('guard_name', $guard),
            ],
            'guard' => ['required', 'string', AuthGuard::rule()],
            'permissions' => ['array'],
            'permissions.*' => [
                'string',
                Rule::exists('rbac_permissions', 'name')->where('guard_name', $guard),
            ],
        ];
    }
}
