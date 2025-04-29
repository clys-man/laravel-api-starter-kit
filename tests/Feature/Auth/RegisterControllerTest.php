<?php

declare(strict_types=1);

use App\Models\User;

it('registers a new user successfully', function (): void {
    $response = $this->postJson(route('auth:register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertCreated();
    $response->assertJsonStructure([
        'user' => [
            'id',
            'name',
            'email',
        ],
        'token',
    ]);

    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
});

it('returns 422 when validation fails during registration', function (): void {
    $response = $this->postJson(route('auth:register'), [
        'email' => 'invalid-email-format',
        'password' => 'pass',
        'password_confirmation' => 'different_password',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'name',
        'email',
        'password',
    ]);
});


it('returns 422 when email is already taken', function (): void {
    User::factory()->create([
        'email' => 'duplicate@example.com',
    ]);

    $response = $this->postJson(route('auth:register'), [
        'name' => 'New User',
        'email' => 'duplicate@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});

it('returns 422 when passwords do not match', function (): void {
    $response = $this->postJson(route('auth:register'), [
        'name' => 'Mismatch User',
        'email' => 'mismatch@example.com',
        'password' => 'password123',
        'password_confirmation' => 'differentPassword',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['password']);
});
