<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts;

interface ArrayConvertible
{
    /**
     * @template T of array|null
     * @param T $data
     * @return (T is null ? null : static)
     */
    public static function fromArray(?array $data): ?static;

    public function toArray(): array;
}
