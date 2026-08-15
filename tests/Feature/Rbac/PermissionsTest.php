<?php

test('guests receive 401 for the permissions index api', function () {
    $this->getJson(route('api.permissions.index'))->assertUnauthorized();
});

test('users without permission receive 403 for the permissions index api', function () {
    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->getJson(route('api.permissions.index'))->assertForbidden();
});

test('permissions index returns grouped permissions for a guard', function () {
    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->getJson(route('api.permissions.index', ['guard' => 'sanctum']))
        ->assertOk()
        ->assertJsonPath('data.users.0', 'users.index')
        ->assertJsonPath('data.rbac.0', 'rbac.index');
});

test('permissions index requires a valid guard', function () {
    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->getJson(route('api.permissions.index'))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['guard']);
});
