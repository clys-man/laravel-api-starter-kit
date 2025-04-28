<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\DatabaseManager;

/**
 * @extends EloquentRepository<User>
 *
 * @implements UserRepositoryInterface<User>
 */
final class EloquentUserRepository extends EloquentRepository implements UserRepositoryInterface
{
    public function __construct(
        User $model,
        DatabaseManager $database
    ) {
        parent::__construct($model, $database);
    }
}
