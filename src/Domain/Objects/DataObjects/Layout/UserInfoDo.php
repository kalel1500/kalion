<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\AbstractDataObject;

final class UserInfoDo extends AbstractDataObject
{
    public function __construct(
        public readonly string $name,
        public readonly string $email
    )
    {
    }
}
