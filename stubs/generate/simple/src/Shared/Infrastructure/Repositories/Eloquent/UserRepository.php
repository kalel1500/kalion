<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Repositories\Eloquent;

use Src\Shared\Domain\Objects\Entities\UserEntity;
use \Thehouseofel\Kalion\Infrastructure\Repositories\UserRepository as BaseUserRepository;

final class UserRepository extends BaseUserRepository
{
    public function is_important_group(UserEntity $user): bool
    {
        return $user->id->value() === 4;
    }
}
