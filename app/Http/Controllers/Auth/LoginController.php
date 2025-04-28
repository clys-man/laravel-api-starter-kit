<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\DTO\Auth\LoginDTO;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final readonly class LoginController
{
    public function __construct(
        private AuthService $service,
    ) {}

    /**
     * @throws ValidationException
     */
    public function __invoke(LoginRequest $request): Response
    {
        /** @var string $email */
        $email = $request->input('email');
        /** @var string $password */
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

        try {
            $token = $this->service->login(
                new LoginDTO(
                    email: $email,
                    password: $password,
                ),
                $remember
            );

            RateLimiter::clear($throttleKey);

            return new JsonResponse([
                'token' => $token?->plainTextToken,
            ]);
        } catch (RuntimeException) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }
    }
}
