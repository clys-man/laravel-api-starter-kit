<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\RepositoryInterface;
use Illuminate\Container\Attributes\Bind;

/**
 * @extends RepositoryInterface<User>
 */
#[Bind(EloquentUserRepository::class)]
interface UserRepositoryInterface extends RepositoryInterface {}
