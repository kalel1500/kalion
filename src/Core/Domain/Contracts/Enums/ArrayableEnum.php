<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Contracts\Enums;

interface ArrayableEnum
{
    public static function toArray(): array;
}
