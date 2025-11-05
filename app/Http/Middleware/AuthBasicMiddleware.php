<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AuthBasicMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->isLocal()) {
            return $next($request);
        }

        $user = $request->getUser();
        $password = $request->getPassword();

        if (in_array($user, [null, '', '0'], true) || (in_array($password, [null, '', '0'], true))) {
            return response('Unauthorized', 401)->header('WWW-Authenticate', 'Basic');
        }

        $userConfig = config('app.credentials.username');
        $passConfig = config('app.credentials.password');

        if (
            $user !== $userConfig ||
            $password !== $passConfig
        ) {
            return response('Unauthorized', 401)->header('WWW-Authenticate', 'Basic');
        }

        return $next($request);
    }
}
