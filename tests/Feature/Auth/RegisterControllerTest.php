<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

use function Pest\Laravel\postJson;

beforeEach(function () {
    RateLimiter::clear('');
});

it('registers a new user successfully', function () {
    $response = postJson(route('register'), [
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

it('blocks registration after too many attempts', function () {
    $key = strtolower('test@example.com') . '|' . '127.0.0.1';
    $key = \Illuminate\Support\Str::transliterate($key);

    RateLimiter::hit($key);
    RateLimiter::hit($key);
    RateLimiter::hit($key);
    RateLimiter::hit($key);
    RateLimiter::hit($key);

    $response = postJson(route('register'), [
        'name' => 'Blocked User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('email');
});
