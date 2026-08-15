<?php

namespace App\Features\Rbac\Roles\Update;

use App\Enums\AuthGuard;
use App\Enums\UserRole;
use App\Models\RBAC\Role;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var Role $role */
        $role = $this->route('role');

        return self::rulesFor($this->input('guard'), $role);
    }

    /**
     * Single source of validation rules for the Update slice,
     * shared by this request and the Livewire page.
     *
     * @return array<string, mixed>
     */
    public static function rulesFor(?string $guard, Role $role): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rbac_roles', 'name')
                    ->where('guard_name', $guard)
                    ->ignore($role->id),
            ],
            'guard' => ['required', 'string', AuthGuard::rule()],
            'permissions' => [
                'array',
                static function (string $attribute, mixed $value, Closure $fail) use ($role): void {
                    // The super-admin role must always retain at least one permission.
                    if ($role->name === UserRole::SUPER_ADMIN->value && $value === []) {
                        $fail(__('The super-admin role must keep at least one permission.'));
                    }
                },
            ],
            'permissions.*' => [
                'string',
                Rule::exists('rbac_permissions', 'name')->where('guard_name', $guard),
            ],
        ];
    }
}
