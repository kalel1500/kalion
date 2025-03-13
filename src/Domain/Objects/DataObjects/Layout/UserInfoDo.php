<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\ContractDataObject;

final class UserInfoDo extends ContractDataObject
{
    public function __construct(
        public readonly string $name,
        public readonly string $email
    )
    {
    }
}
