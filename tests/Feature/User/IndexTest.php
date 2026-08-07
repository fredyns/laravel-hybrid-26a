<?php

use App\Features\User\Index\IndexUsersPage;
use App\Models\User;
use Livewire\Livewire;

test('index page is displayed for authorized user', function () {
    $this->actingAs(adminUser());

    $this->get(route('users.index'))->assertOk();
});

test('index page is forbidden without permission', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('users.index'))->assertForbidden();
});

test('index can search users by name', function () {
    $this->actingAs(adminUser());

    User::factory()->create(['name' => 'Zentavia Marwick', 'email' => 'zentavia@example.test']);
    User::factory()->create(['name' => 'Bartholomew Quince', 'email' => 'bartholomew@example.test']);

    $this->get(route('users.index', ['q' => 'Zentavia']))
        ->assertOk()
        ->assertSee('Zentavia Marwick')
        ->assertDontSee('Bartholomew Quince');
});

test('index can search users by email', function () {
    $this->actingAs(adminUser());

    User::factory()->create(['name' => 'Zentavia Marwick', 'email' => 'zentavia@example.test']);
    User::factory()->create(['name' => 'Bartholomew Quince', 'email' => 'bartholomew@example.test']);

    $this->get(route('users.index', ['q' => 'bartholomew@example.test']))
        ->assertOk()
        ->assertSee('Bartholomew Quince')
        ->assertDontSee('Zentavia Marwick');
});

test('index search updates live', function () {
    $this->actingAs(adminUser());

    User::factory()->create(['name' => 'Zentavia Marwick']);
    User::factory()->create(['name' => 'Bartholomew Quince']);

    Livewire::test(IndexUsersPage::class)
        ->assertSee('Zentavia Marwick')
        ->assertSee('Bartholomew Quince')
        ->set('search', 'Zentavia')
        ->assertSee('Zentavia Marwick')
        ->assertDontSee('Bartholomew Quince');
});

test('index can filter visible columns', function () {
    $this->actingAs(adminUser());

    User::factory()->create(['name' => 'Columnar Tester', 'email' => 'columnar@example.test']);

    Livewire::test(IndexUsersPage::class)
        ->set('visibleColumns', ['name'])
        ->assertSee('Columnar Tester')
        ->assertDontSee('columnar@example.test')
        ->set('visibleColumns', ['name', 'email'])
        ->assertSee('Columnar Tester')
        ->assertSee('columnar@example.test');
});

test('index sorts by column through the sort action', function () {
    $this->actingAs(adminUser());

    User::factory()->create(['name' => 'Aaa First']);
    User::factory()->create(['name' => 'Zzz Last']);

    Livewire::test(IndexUsersPage::class)
        ->call('sortBy', 'name')
        ->assertSet('sort', 'name')
        ->assertSet('direction', 'asc')
        ->assertSeeInOrder(['Aaa First', 'Zzz Last'])
        ->call('sortBy', 'name')
        ->assertSet('direction', 'desc')
        ->assertSeeInOrder(['Zzz Last', 'Aaa First']);
});

test('index ignores unknown sort columns', function () {
    $this->actingAs(adminUser());

    Livewire::test(IndexUsersPage::class)
        ->call('sortBy', 'password')
        ->assertSet('sort', 'created_at');
});

test('guests receive 401 for the users index api', function () {
    $this->getJson(route('api.users.index'))->assertUnauthorized();
});

test('users without permission receive 403 for the users index api', function () {
    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->getJson(route('api.users.index'))->assertForbidden();
});

test('index returns paginated users', function () {
    User::factory()->count(3)->create();

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->getJson(route('api.users.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at']],
            'meta' => ['current_page', 'per_page', 'total', 'from', 'to'],
        ])
        ->assertJsonPath('meta.total', User::count());
});

test('index filters users by search term', function () {
    User::factory()->create(['name' => 'Findable Person']);
    User::factory()->create(['name' => 'Somebody Else']);

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->getJson(route('api.users.index', ['search' => 'Findable']))
        ->assertOk()
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.name', 'Findable Person');
});

test('index sorts users by the requested column', function () {
    User::factory()->create(['name' => 'Aaa First']);
    User::factory()->create(['name' => 'Zzz Last']);

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->getJson(route('api.users.index', ['sort_by' => 'name', 'sort_dir' => 'asc']))
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Aaa First');
});

test('index rejects unknown sort columns', function () {
    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->getJson(route('api.users.index', ['sort_by' => 'password']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['sort_by']);
});
