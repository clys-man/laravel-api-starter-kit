<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

/**
 * @extends EloquentRepository<User>
 *
 * @implements UserRepositoryInterface<User>
 */
final class EloquentUserRepository extends EloquentRepository implements UserRepositoryInterface
{
    public function __construct(
        User $model,
    ) {
        parent::__construct($model);
    }
}
