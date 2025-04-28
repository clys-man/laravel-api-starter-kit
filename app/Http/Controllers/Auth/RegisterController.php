<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\DTO\Auth\RegisterDTO;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class RegisterController
{
    public function __invoke(
        RegisterRequest $request,
        AuthService $service
    ): Response {
        /** @var string $email */
        $email = $request->input('email');
        $ip = $request->ip();

        $throttleKey = Str::transliterate(Str::lower($email) . '|' . $ip);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            event(new Lockout($request));

            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        try {
            $user = $service->register(
                RegisterDTO::from($request->validated())
            );

            $token = $user->createToken(
                name: 'API Access Token',
                abilities: ['*']
            );

            RateLimiter::clear($throttleKey);

            return new JsonResponse([
                'user' => new UserResource(resource: $user),
                'token' => $token->plainTextToken,
            ], Response::HTTP_CREATED);
        } catch (Throwable) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }
    }
}
