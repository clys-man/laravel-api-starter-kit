<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;
use Symfony\Component\HttpFoundation\Response;

final readonly class LoginController
{
    /**
     * @throws ValidationException
     */
    public function __invoke(LoginRequest $request): Response
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $remember = $request->boolean('remember');
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

        if ( ! Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($throttleKey);

        /** @var User|null $user */
        $user = $request->user();

        /** @var NewAccessToken $token */
        $token = $user?->createToken(
            name: 'API Access Token',
            abilities: ['*']
        );

        return new JsonResponse([
            'token' => $token->plainTextToken,
        ]);
    }
}
