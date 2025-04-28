<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('logs out the authenticated user', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson(route('auth:logout'));

    $response->assertNoContent();

    expect($user->tokens()->count())->toBe(0);
});
