<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Auth\AuthService;
use App\Services\Auth\AuthServiceInterface;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        AuthServiceInterface::class => AuthService::class,
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimit();
        $this->configureCommands();
        $this->configureModels();
        $this->configurePasswordValidation();
        $this->configureDates();
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );
    }

    private function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function configureModels(): void
    {
        Model::shouldBeStrict(! app()->isProduction());
        Model::unguard();
    }

    private function configurePasswordValidation(): void
    {
        Password::defaults(fn() => app()->isProduction() ? Password::min(8)->uncompromised() : null);
    }

    private function configureRateLimit(): void
    {
        RateLimiter::for(
            name: 'api',
            callback: static fn(Request $request) => [
                Limit::perMinute(
                    maxAttempts: 6000,
                )->by(
                    key: $request->bearerToken(),
                ),
                Limit::perSecond(
                    maxAttempts: 200,
                )->by(
                    key: $request->bearerToken(),
                ),
            ],
        );
    }
}
