<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function (): void {
    RateLimiter::clear('');
});

it('allows a user to login successfully', function (): void {
    $password = 'password';
    $user = User::factory()->create([
        'password' => bcrypt($password),
    ]);

    $response = $this->postJson(route('auth:login'), [
        'email' => $user->email,
        'password' => $password,
    ]);

    $response->assertOk();
    $response->assertJsonStructure(['token']);
});

it('returns validation error if credentials are invalid', function (): void {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson(route('auth:login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('email');
});
