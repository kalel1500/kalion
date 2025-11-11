<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Repositories\Eloquent;

use Src\Shared\Domain\Objects\Entities\UserEntity;
use Thehouseofel\Kalion\Core\Infrastructure\Repositories\Eloquent\EloquentUserRepository as BaseUserRepository;

final class EloquentUserRepository extends BaseUserRepository
{
    public function is_important_group(UserEntity $user): bool
    {
        return $user->id->value === 4;
    }
}
