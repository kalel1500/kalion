<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Contracts;

interface ArrayResolvable
{
    /**
     * @template T of array|null
     * @param T $data
     * @return (T is null ? null : static)
     */
    public static function resolveFromArray(?array $data): ?static;
}
