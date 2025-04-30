<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

/**
 * @implements UserRepositoryInterface<User>
 */
final class EloquentUserRepository extends EloquentRepository implements UserRepositoryInterface {}
