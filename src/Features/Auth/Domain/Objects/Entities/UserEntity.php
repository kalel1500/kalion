<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities;

use Thehouseofel\Kalion\Core\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Concerns\HasGuard;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AuthenticatableEntity;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\Concerns\HasPermissions;

class UserEntity extends AbstractEntity implements AuthenticatableEntity
{
    use HasPermissions, HasGuard;

    public function __construct(
        public readonly IdVo|IdNullVo $id,
        public readonly StringVo      $name,
        public readonly StringVo      $email,
        public readonly StringNullVo  $email_verified_at,
    )
    {
    }
}
