<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Layout;

use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;

class UserInfoDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly int    $id,
        public readonly string $name,
        public readonly string $email
    )
    {
    }
}
