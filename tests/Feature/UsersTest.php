<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\PermissionRegistrar;

function adminUser(): User
{
    $user = User::factory()->create();
    $user->addRoleNames([UserRole::SUPER_ADMIN]);

    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $user;
}

function externalUser(): User
{
    $user = User::factory()->create();
    $user->addRoleNames([UserRole::EXTERNAL]);

    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $user;
}

test('index page is displayed for authorized user', function () {
    $this->actingAs(adminUser());

    $this->get(route('users.index'))->assertOk();
});

test('index page is forbidden without permission', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('users.index'))->assertForbidden();
});

test('index can search users by name and email', function () {
    $this->actingAs(adminUser());

    $needle = User::factory()->create(['name' => 'Zentavia Marwick', 'email' => 'zentavia@example.test']);
    $other = User::factory()->create(['name' => 'Bartholomew Quince', 'email' => 'bartholomew@example.test']);

    Livewire::test('pages::users.index')
        ->set('search', 'Zentavia')
        ->assertSee('Zentavia Marwick')
        ->assertDontSee('Bartholomew Quince')
        ->set('search', 'bartholomew@example.test')
        ->assertSee('Bartholomew Quince')
        ->assertDontSee('Zentavia Marwick');
});

test('index only renders selected columns', function () {
    $this->actingAs(adminUser());

    $user = User::factory()->create(['name' => 'Columnar Tester', 'email' => 'columnar@example.test']);

    Livewire::test('pages::users.index')
        ->set('search', 'Columnar')
        ->set('visibleColumns', ['name'])
        ->assertSee('Columnar Tester')
        ->assertDontSee('columnar@example.test')
        ->set('visibleColumns', ['name', 'email'])
        ->assertSee('columnar@example.test');
});

test('a user can be created through the form modal', function () {
    $this->actingAs(adminUser());

    Livewire::test('pages::users.form')
        ->call('createUser')
        ->set('name', 'Created Person')
        ->set('email', 'created@example.test')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('users-updated');

    expect(User::where('email', 'created@example.test')->exists())->toBeTrue();
});

test('creating a user validates input', function () {
    $this->actingAs(adminUser());

    Livewire::test('pages::users.form')
        ->call('createUser')
        ->set('name', '')
        ->set('email', 'not-an-email')
        ->set('password', 'password')
        ->set('password_confirmation', 'different')
        ->call('save')
        ->assertHasErrors(['name', 'email', 'password']);
});

test('an existing user can be edited through the form modal', function () {
    $this->actingAs(adminUser());

    $user = User::factory()->create(['name' => 'Before Name']);

    Livewire::test('pages::users.form')
        ->call('editUser', userId: $user->id)
        ->assertSet('name', 'Before Name')
        ->set('name', 'After Name')
        ->set('email', 'after@example.test')
        ->call('save')
        ->assertHasNoErrors();

    expect($user->refresh()->name)->toBe('After Name');
    expect($user->email)->toBe('after@example.test');
});

test('a user can be deleted from the show page', function () {
    $this->actingAs(adminUser());

    $user = User::factory()->create();

    Livewire::test('pages::users.show', ['user' => $user])
        ->call('delete')
        ->assertRedirect(route('users.index'));

    expect(User::find($user->id))->toBeNull();
});

test('a password can be changed through the modal', function () {
    $this->actingAs(adminUser());

    $user = User::factory()->create();

    Livewire::test('pages::users.change-password')
        ->call('open', userId: $user->id)
        ->set('password', 'new-password-123')
        ->set('password_confirmation', 'new-password-123')
        ->call('updatePassword')
        ->assertHasNoErrors();

    expect(Hash::check('new-password-123', $user->refresh()->password))->toBeTrue();
});

test('changing a password requires confirmation', function () {
    $this->actingAs(adminUser());

    $user = User::factory()->create();

    Livewire::test('pages::users.change-password')
        ->call('open', userId: $user->id)
        ->set('password', 'new-password-123')
        ->set('password_confirmation', 'mismatch')
        ->call('updatePassword')
        ->assertHasErrors('password');
});

test('the show modal loads the selected user', function () {
    $this->actingAs(adminUser());

    $user = User::factory()->create(['name' => 'Modal Subject']);

    Livewire::test('pages::users.show-modal')
        ->call('showUser', userId: $user->id)
        ->assertSet('user.id', $user->id)
        ->assertSee('Modal Subject');
});

test('the individual show page renders', function () {
    $this->actingAs(adminUser());

    $user = User::factory()->create(['name' => 'Page Subject']);

    $this->get(route('users.show', $user))
        ->assertOk()
        ->assertSee('Page Subject');
});

test('the individual show page is forbidden without permission', function () {
    $this->actingAs(User::factory()->create());

    $user = User::factory()->create();

    $this->get(route('users.show', $user))->assertForbidden();
});

test('the show page opens the edit modal via event', function () {
    $this->actingAs(adminUser());

    $user = User::factory()->create();

    Livewire::test('pages::users.show', ['user' => $user])
        ->call('edit')
        ->assertDispatched('user-edit');
});

test('the show page opens the change password modal via event', function () {
    $this->actingAs(adminUser());

    $user = User::factory()->create();

    Livewire::test('pages::users.show', ['user' => $user])
        ->call('changePassword')
        ->assertDispatched('user-change-password');
});

test('the show page refreshes the displayed user after an update', function () {
    $this->actingAs(adminUser());

    $user = User::factory()->create(['name' => 'Stale Name']);

    $component = Livewire::test('pages::users.show', ['user' => $user]);

    $user->update(['name' => 'Fresh Name']);

    $component->call('refreshUser')
        ->assertSet('user.name', 'Fresh Name')
        ->assertSee('Fresh Name');
});

test('a user with only index permission cannot create', function () {
    $this->actingAs(externalUser());

    Livewire::test('pages::users.form')
        ->call('createUser')
        ->assertForbidden();
});
