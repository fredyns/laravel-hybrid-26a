<?php

use App\Enums\AuthGuard;
use App\Enums\UserRole;
use App\Models\RBAC\Permission;
use App\Models\RBAC\Role;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public ?string $roleId = null;

    public string $name = '';

    public string $guard = 'web';

    /**
     * The permission names currently selected for the role.
     *
     * @var array<int, string>
     */
    public array $selectedPermissions = [];

    public bool $protected = false;

    public function isEditing(): bool
    {
        return $this->roleId !== null;
    }

    #[On('role-create')]
    public function createRole(): void
    {
        $this->authorize('rbac.create');

        $this->resetForm();

        $this->modal('role-form')->show();
    }

    #[On('role-edit')]
    public function editRole(string $roleId): void
    {
        $this->authorize('rbac.update');

        $role = Role::query()->with('permissions:id,name')->findOrFail($roleId);

        $this->resetForm();
        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->guard = $role->guard_name;
        $this->protected = $role->isProtected();
        $this->selectedPermissions = $role->permissions->pluck('name')->all();

        $this->modal('role-form')->show();
    }

    public function updatedGuard(): void
    {
        // Permissions are guard-specific, so a guard switch clears the selection.
        $this->selectedPermissions = [];
    }

    public function save(): void
    {
        $this->authorize($this->isEditing() ? 'rbac.update' : 'rbac.create');

        $validated = $this->validate($this->rules());

        if ($this->isEditing()) {
            $role = Role::query()->findOrFail($this->roleId);

            // Protected roles keep their name and guard locked.
            if (! $role->isProtected()) {
                $role->name = $validated['name'];
                $role->guard_name = $validated['guard'];
                $role->save();
            }

            // The super-admin role must always retain at least one permission.
            if ($role->name === UserRole::SUPER_ADMIN->value && empty($validated['selectedPermissions'])) {
                $this->addError('selectedPermissions', __('The super-admin role must keep at least one permission.'));

                return;
            }

            $role->syncPermissions($validated['selectedPermissions']);
        } else {
            $role = Role::create([
                'name' => $validated['name'],
                'guard_name' => $validated['guard'],
            ]);

            if (! empty($validated['selectedPermissions'])) {
                $role->syncPermissions($validated['selectedPermissions']);
            }
        }

        Flux::toast(variant: 'success', text: $this->isEditing() ? __('Role updated.') : __('Role created.'));

        $this->dispatch('roles-updated');

        $this->modal('role-form')->close();

        $this->resetForm();
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('rbac_roles', 'name')->where('guard_name', $this->guard)->ignore($this->roleId)],
            'guard' => ['required', 'string', AuthGuard::rule()],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['string', Rule::exists('rbac_permissions', 'name')->where('guard_name', $this->guard)],
        ];
    }

    /**
     * Permissions available for the selected guard, grouped by resource prefix
     * and ordered by the conventional CRUD action sequence.
     *
     * @return array<string, array<int, string>>
     */
    #[Computed]
    public function permissionGroups(): array
    {
        $order = ['index', 'show', 'create', 'update', 'delete'];

        return Permission::query()
            ->where('guard_name', $this->guard)
            ->pluck('name')
            ->groupBy(fn (string $name) => Str::before($name, '.'))
            ->map(fn ($group) => $group
                ->sortBy(function (string $name) use ($order) {
                    $action = Str::after($name, '.');
                    $index = array_search($action, $order, true);

                    return $index === false ? count($order) : $index;
                })
                ->values()
                ->all())
            ->sortKeys()
            ->all();
    }

    protected function resetForm(): void
    {
        $this->reset(['roleId', 'name', 'guard', 'selectedPermissions', 'protected']);
        $this->resetValidation();
    }
}; ?>

<flux:modal name="role-form" class="w-full max-w-2xl">
    <form wire:submit="save" class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $this->isEditing() ? __('Edit role') : __('Create role') }}</flux:heading>
            <flux:subheading>
                {{ $this->isEditing() ? __('Update this role and its permissions.') : __('Define a new role and assign permissions.') }}
            </flux:subheading>
        </div>

        <flux:input
            wire:model="name"
            :label="__('Name')"
            type="text"
            required
            autofocus
            autocomplete="off"
            :readonly="$protected"
            :description="$protected ? __('Protected role names cannot be changed.') : null"
            :invalid="$errors->has('name')"
        />
        <flux:error name="name" />

        <flux:select wire:model.live="guard" :label="__('Guard')" :disabled="$protected" :invalid="$errors->has('guard')">
            @foreach (\App\Enums\AuthGuard::values() as $guardValue)
                <flux:select.option value="{{ $guardValue }}">{{ $guardValue }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:error name="guard" />

        <div class="space-y-2">
            <flux:text class="font-medium">{{ __('Permissions') }}</flux:text>

            <div class="max-h-[45vh] space-y-5 overflow-y-auto rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                @forelse ($this->permissionGroups as $resource => $permissions)
                    <flux:checkbox.group wire:key="group-{{ $guard }}-{{ $resource }}">
                        <flux:checkbox.all :label="Str::headline($resource)" />

                        <div class="ms-6 mt-2 grid gap-2 sm:grid-cols-2">
                            @foreach ($permissions as $permission)
                                <flux:checkbox
                                    wire:model="selectedPermissions"
                                    value="{{ $permission }}"
                                    :label="Str::headline(Str::after($permission, '.'))"
                                />
                            @endforeach
                        </div>
                    </flux:checkbox.group>
                @empty
                    <flux:text class="py-2 text-center">{{ __('No permissions available for this guard.') }}</flux:text>
                @endforelse
            </div>

            <flux:error name="selectedPermissions" />
        </div>

        <div class="flex justify-end gap-2">
            <flux:modal.close>
                <flux:button variant="filled" type="button">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>

            <flux:button variant="primary" type="submit">
                {{ $this->isEditing() ? __('Save changes') : __('Create role') }}
            </flux:button>
        </div>
    </form>
</flux:modal>
