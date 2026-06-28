<?php

use App\Models\RBAC\Role;
use App\Models\User;
use Livewire\Livewire;

test('roles index is displayed for authorized user', function () {
    $this->actingAs(adminUser());

    $this->get(route('roles.index'))->assertOk();
});

test('roles index is forbidden without permission', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('roles.index'))->assertForbidden();
});

test('index can search roles by name', function () {
    $this->actingAs(adminUser());

    Role::create(['name' => 'zentavia-moderator', 'guard_name' => 'web']);

    Livewire::test('pages::roles.index')
        ->set('search', 'zentavia')
        ->assertSee('zentavia-moderator')
        ->assertDontSee('super-admin');
});

test('index can filter roles by guard', function () {
    $this->actingAs(adminUser());

    Role::create(['name' => 'sanctum-only-role', 'guard_name' => 'sanctum']);

    Livewire::test('pages::roles.index')
        ->call('filterByGuard', 'sanctum')
        ->assertSee('sanctum-only-role')
        ->call('filterByGuard', 'web')
        ->assertDontSee('sanctum-only-role')
        ->call('filterByGuard', '')
        ->assertSee('sanctum-only-role');
});

test('a role can be created through the form modal', function () {
    $this->actingAs(adminUser());

    Livewire::test('pages::roles.form')
        ->call('createRole')
        ->set('name', 'editor')
        ->set('guard', 'web')
        ->set('selectedPermissions', ['users.index', 'users.show'])
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('roles-updated');

    $role = Role::where('name', 'editor')->where('guard_name', 'web')->first();

    expect($role)->not->toBeNull();
    expect($role->permissions->pluck('name')->all())->toContain('users.index', 'users.show');
});

test('creating a role validates the name', function () {
    $this->actingAs(adminUser());

    Role::create(['name' => 'duplicate', 'guard_name' => 'web']);

    Livewire::test('pages::roles.form')
        ->call('createRole')
        ->set('name', 'duplicate')
        ->set('guard', 'web')
        ->call('save')
        ->assertHasErrors('name');
});

test('an existing custom role can be edited through the form modal', function () {
    $this->actingAs(adminUser());

    $role = Role::create(['name' => 'before-name', 'guard_name' => 'web']);

    Livewire::test('pages::roles.form')
        ->call('editRole', roleId: $role->id)
        ->assertSet('name', 'before-name')
        ->assertSet('protected', false)
        ->set('name', 'after-name')
        ->set('selectedPermissions', ['users.index'])
        ->call('save')
        ->assertHasNoErrors();

    expect($role->refresh()->name)->toBe('after-name');
    expect($role->permissions->pluck('name')->all())->toContain('users.index');
});

test('a protected role keeps its name when edited', function () {
    $this->actingAs(adminUser());

    $role = Role::where('name', 'internal')->where('guard_name', 'web')->firstOrFail();

    Livewire::test('pages::roles.form')
        ->call('editRole', roleId: $role->id)
        ->assertSet('protected', true)
        ->set('name', 'tampered')
        ->set('selectedPermissions', ['users.index'])
        ->call('save')
        ->assertHasNoErrors();

    expect($role->refresh()->name)->toBe('internal');
    expect($role->permissions->pluck('name')->all())->toContain('users.index');
});

test('the super-admin role must keep at least one permission', function () {
    $this->actingAs(adminUser());

    $role = Role::where('name', 'super-admin')->where('guard_name', 'web')->firstOrFail();

    Livewire::test('pages::roles.form')
        ->call('editRole', roleId: $role->id)
        ->set('selectedPermissions', [])
        ->call('save')
        ->assertHasErrors('selectedPermissions');

    expect($role->permissions()->count())->toBeGreaterThan(0);
});

test('the role show page renders', function () {
    $this->actingAs(adminUser());

    $role = Role::where('name', 'super-admin')->where('guard_name', 'web')->firstOrFail();

    $this->get(route('roles.show', $role))
        ->assertOk()
        ->assertSee('super-admin');
});

test('the role show page is forbidden without permission', function () {
    $this->actingAs(User::factory()->create());

    $role = Role::where('name', 'super-admin')->where('guard_name', 'web')->firstOrFail();

    $this->get(route('roles.show', $role))->assertForbidden();
});

test('a custom role can be deleted from the show page', function () {
    $this->actingAs(adminUser());

    $role = Role::create(['name' => 'disposable', 'guard_name' => 'web']);

    Livewire::test('pages::roles.show', ['role' => $role])
        ->call('delete')
        ->assertRedirect(route('roles.index'));

    expect(Role::find($role->id))->toBeNull();
});

test('a protected role cannot be deleted', function () {
    $this->actingAs(adminUser());

    $role = Role::where('name', 'internal')->where('guard_name', 'web')->firstOrFail();

    Livewire::test('pages::roles.show', ['role' => $role])
        ->call('delete')
        ->assertNoRedirect();

    expect(Role::find($role->id))->not->toBeNull();
});

test('a role with assigned users cannot be deleted', function () {
    $this->actingAs(adminUser());

    $role = Role::create(['name' => 'staffed', 'guard_name' => 'web']);
    User::factory()->create()->assignRole($role);

    Livewire::test('pages::roles.show', ['role' => $role])
        ->call('delete')
        ->assertNoRedirect();

    expect(Role::find($role->id))->not->toBeNull();
});

test('the show page opens the edit modal via event', function () {
    $this->actingAs(adminUser());

    $role = Role::create(['name' => 'eventful', 'guard_name' => 'web']);

    Livewire::test('pages::roles.show', ['role' => $role])
        ->call('edit')
        ->assertDispatched('role-edit');
});

test('a user without rbac permission cannot create a role', function () {
    $this->actingAs(externalUser());

    Livewire::test('pages::roles.form')
        ->call('createRole')
        ->assertForbidden();
});
