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
