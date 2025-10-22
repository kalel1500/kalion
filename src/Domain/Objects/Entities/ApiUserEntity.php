<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Objects\Entities\Concerns\EntityHasPermissions;
use Thehouseofel\Kalion\Domain\Objects\Entities\Concerns\HasGuard;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\StringVo;

class ApiUserEntity extends AbstractEntity
{
    use EntityHasPermissions, HasGuard;

    public function __construct(
        public readonly IdVo|IdNullVo $id,
        public readonly StringVo      $name,
    )
    {
    }
}
