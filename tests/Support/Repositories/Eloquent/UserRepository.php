<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Repositories\Eloquent;

use Thehouseofel\Kalion\Infrastructure\Repositories\Eloquent\UserRepository as BaseUserRepository;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\UserEntity;

final class UserRepository extends BaseUserRepository
{
    public function is_important_group(UserEntity $user): bool
    {
        return $user->id->value() === 4;
    }
}
