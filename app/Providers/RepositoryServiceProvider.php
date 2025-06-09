<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Repositories\User\EloquentUserRepository;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->bind(UserRepositoryInterface::class, fn(): EloquentUserRepository => new EloquentUserRepository(new User()));
    }
}
