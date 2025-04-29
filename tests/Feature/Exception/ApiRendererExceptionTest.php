<?php

declare(strict_types=1);

use App\Exceptions\ApiExceptionRenderer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

it('renders a validation exception with status 422', function (): void {
    App::shouldReceive('isLocal')->andReturnFalse();

    $exception = ValidationException::withMessages([
        'email' => ['The email field is required.'],
    ]);

    $response = (new ApiExceptionRenderer($exception))->render();

    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->content())
        ->json()
        ->status->toBe(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->title->toBe('Unprocessable Entity')
        ->detail->toBe('The email field is required.');
});

it('renders a bad request exception with status 400', function (): void {
    app()->detectEnvironment(fn () => 'production');

    $exception = new BadRequestException('Malformed request');
    $response = (new ApiExceptionRenderer($exception))->render();

    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getContent())
        ->json()
        ->status->toBe(Response::HTTP_BAD_REQUEST)
        ->title->toBe('Bad Request')
        ->detail->toBe('Malformed request');
});

it('renders an authentication exception with status 401', function (): void {
    App::shouldReceive('isLocal')->andReturnFalse();

    $exception = new AuthenticationException('Not authenticated');
    $response = (new ApiExceptionRenderer($exception))->render();

    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getContent())
        ->json()
        ->status->toBe(Response::HTTP_UNAUTHORIZED)
        ->title->toBe('Unauthorized')
        ->detail->toBe('Not authenticated');
});

it('renders an authorization exception with status 403', function (): void {
    App::shouldReceive('isLocal')->andReturnFalse();

    $exception = new AuthorizationException('Access denied');
    $response = (new ApiExceptionRenderer($exception))->render();

    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getContent())
        ->json()
        ->status->toBe(Response::HTTP_FORBIDDEN)
        ->title->toBe('Forbidden')
        ->detail->toBe('Access denied');
});

it('renders a not found exception with status 404', function (): void {
    App::shouldReceive('isLocal')->andReturnFalse();

    $exception = new NotFoundHttpException('Resource not found');
    $response = (new ApiExceptionRenderer($exception))->render();

    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getContent())
        ->json()
        ->status->toBe(Response::HTTP_NOT_FOUND)
        ->title->toBe('Not Found')
        ->detail->toBe('Resource not found');
});

it('renders a generic exception with status 500', function (): void {
    App::shouldReceive('isLocal')->andReturnFalse();

    $exception = new Exception('Unexpected error');
    $response = (new ApiExceptionRenderer($exception))->render();

    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getContent())
        ->json()
        ->status->toBe(Response::HTTP_INTERNAL_SERVER_ERROR)
        ->title->toBe('Internal Server Error')
        ->detail->toBe('Unexpected error');
});

it('includes debug info in local environment', function (): void {
    App::shouldReceive('isLocal')->andReturnTrue();

    $exception = new Exception('Debug test');
    $response = (new ApiExceptionRenderer($exception))->render();

    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getContent())
        ->json()
        ->status->toBe(Response::HTTP_INTERNAL_SERVER_ERROR)
        ->exception->toBe(Exception::class)
        ->trace->toBeArray();
});

it('hides debug info outside of local environment', function (): void {
    App::shouldReceive('isLocal')->andReturnFalse();

    $exception = new Exception('Secure error');
    $response = (new ApiExceptionRenderer($exception))->render();

    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getContent())
        ->json()
        ->not->toHaveKeys(['exception', 'trace']);
});
