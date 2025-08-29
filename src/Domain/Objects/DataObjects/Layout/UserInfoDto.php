<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\AbstractDataTransferObject;

class UserInfoDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly string $name,
        public readonly string $email
    )
    {
    }
}
