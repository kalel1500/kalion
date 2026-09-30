<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Contracts;

interface ArrayResolvable
{
    public static function resolveFromArray(?array $data): ?static;
}
