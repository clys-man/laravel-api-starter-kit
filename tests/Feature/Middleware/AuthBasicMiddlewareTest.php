<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function (): void {
    Route::middleware('basic')->get('/basic-test', fn (): string => 'ok');

    app()->detectEnvironment(fn (): string => 'testing');
});

function basicAuthHeader(string $user, string $pass): string
{
    return 'Basic ' . base64_encode($user . ':' . $pass);
}

it('bypasses authentication in local environment', function (): void {
    app()->detectEnvironment(fn (): string => 'local');

    $this->getJson('/basic-test')
        ->assertOk()
        ->assertSee('ok');
});

it('returns 401 when credentials are missing', function (): void {
    $this->getJson('/basic-test')
        ->assertStatus(Response::HTTP_UNAUTHORIZED)
        ->assertHeader('WWW-Authenticate', 'Basic');
});

it('returns 401 when username is empty', function (): void {
    $this->getJson('/basic-test', [
        'Authorization' => basicAuthHeader('', 'secret'),
    ])->assertStatus(Response::HTTP_UNAUTHORIZED);
});

it('returns 401 when password is empty', function (): void {
    $this->getJson('/basic-test', [
        'Authorization' => basicAuthHeader('admin', ''),
    ])->assertStatus(Response::HTTP_UNAUTHORIZED);
});

it('returns 401 when username is "0"', function (): void {
    $this->getJson('/basic-test', [
        'Authorization' => basicAuthHeader('0', 'secret'),
    ])->assertStatus(Response::HTTP_UNAUTHORIZED);
});

it('returns 401 when password is "0"', function (): void {
    $this->getJson('/basic-test', [
        'Authorization' => basicAuthHeader('admin', '0'),
    ])->assertStatus(Response::HTTP_UNAUTHORIZED);
});

it('returns 401 for invalid credentials', function (): void {
    config()->set('app.credentials.username', 'admin');
    config()->set('app.credentials.password', 'secret');

    $this->getJson('/basic-test', [
        'Authorization' => basicAuthHeader('wrong', 'creds'),
    ])->assertStatus(Response::HTTP_UNAUTHORIZED);
});

it('allows request with valid credentials', function (): void {
    config()->set('app.credentials.username', 'admin');
    config()->set('app.credentials.password', 'secret');

    $this->getJson('/basic-test', [
        'Authorization' => basicAuthHeader('admin', 'secret'),
    ])->assertOk()->assertSee('ok');
});
