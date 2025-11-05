<?php

declare(strict_types=1);

namespace App\Providers;

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
        Model::automaticallyEagerLoadRelationships();
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
            callback: static fn(Request $request): array => [
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
