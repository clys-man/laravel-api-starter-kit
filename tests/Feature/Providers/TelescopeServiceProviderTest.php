<?php

declare(strict_types=1);

use App\Providers\TelescopeServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Laravel\Telescope\Telescope;

it('does not hide sensitive data when environment is local', function (): void {
    $app = mock(Application::class);
    $app->shouldReceive('environment')
        ->with('local')
        ->andReturnTrue();

    mock(Telescope::class)
        ->expects('hideRequestParameters')
        ->with(['_token'])
        ->never();

    mock(Telescope::class)
        ->expects('hideRequestHeaders')
        ->with([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ])
        ->never();

    (new TelescopeServiceProvider($app))->register();
});
