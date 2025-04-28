<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\postJson;

it('logs out the authenticated user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = postJson(route('logout'));

    $response->assertOk();

    expect($user->tokens()->count())->toBe(0);
});
