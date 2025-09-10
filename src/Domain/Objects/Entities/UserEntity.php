<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelStringNull;
use Thehouseofel\Kalion\Domain\Objects\Entities\Concerns\EntityHasPermissions;
use Thehouseofel\Kalion\Domain\Objects\Entities\Concerns\HasGuard;

class UserEntity extends AbstractEntity
{
    use EntityHasPermissions, HasGuard;

    public function __construct(
        public readonly ModelId|ModelIdNull $id,
        public readonly ModelString     $name,
        public readonly ModelString     $email,
        public readonly ModelStringNull $email_verified_at
    )
    {
    }
}
