<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

use function Pest\Laravel\postJson;

beforeEach(function (): void {
    RateLimiter::clear(''); // Evitar lixo de testes
});

it('allows a user to login successfully', function (): void {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = postJson(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertOk();
    $response->assertJsonStructure(['token']);
});

it('blocks login after too many attempts', function (): void {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $key = mb_strtolower($user->email) . '|' . '127.0.0.1';
    $key = Illuminate\Support\Str::transliterate($key);

    RateLimiter::hit($key);
    RateLimiter::hit($key);
    RateLimiter::hit($key);
    RateLimiter::hit($key);
    RateLimiter::hit($key);

    $response = postJson(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('email');
});

it('returns validation error if credentials are invalid', function (): void {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = postJson(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('email');
});
