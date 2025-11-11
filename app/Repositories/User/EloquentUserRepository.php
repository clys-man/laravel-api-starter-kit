<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\EloquentRepository;

/**
 * @extends EloquentRepository<User>
 */
final class EloquentUserRepository extends EloquentRepository implements UserRepositoryInterface
{
    public function __construct(
        User $user
    ) {
        parent::__construct($user);
    }
}
