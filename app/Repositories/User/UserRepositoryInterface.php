<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Repositories\RepositoryInterface;
use Illuminate\Container\Attributes\Bind;

/**
 * @template TEntity
 *
 * @extends RepositoryInterface<TEntity>
 */
#[Bind(EloquentUserRepository::class)]
interface UserRepositoryInterface extends RepositoryInterface {}
