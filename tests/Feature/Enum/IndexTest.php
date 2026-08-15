<?php

use Illuminate\Support\Facades\File;

test('guests receive 401 for the enums api', function () {
    $this->getJson(route('api.enums.index'))->assertUnauthorized();
});

test('enums index returns an empty payload when nothing is generated', function () {
    File::deleteDirectory(public_path('enums'));

    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->getJson(route('api.enums.index'))
        ->assertOk()
        ->assertExactJson(['data' => []]);
});

test('enums index exposes generated json keyed by nested path', function () {
    File::ensureDirectoryExists(public_path('enums/rbac'));
    File::put(public_path('enums/user-role.json'), json_encode(['super-admin' => 'Super Admin']));
    File::put(public_path('enums/rbac/guard.json'), json_encode(['web' => 'Web']));

    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->getJson(route('api.enums.index'))
        ->assertOk()
        ->assertJsonPath('data.user-role.super-admin', 'Super Admin')
        ->assertJsonPath('data.rbac\.guard.web', 'Web');

    File::deleteDirectory(public_path('enums'));
});
