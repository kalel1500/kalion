<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Contracts;

interface ArrayConvertible
{
    public static function fromArray(?array $data): ?static;

    public function toArray(): array;
}
