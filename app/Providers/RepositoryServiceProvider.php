<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->bind(UserRepositoryInterface::class, fn (): EloquentUserRepository => new EloquentUserRepository(new User()));
    }
}
