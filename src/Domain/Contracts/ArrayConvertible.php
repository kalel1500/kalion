<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts;

interface ArrayConvertible
{
    public function toArray(): array;
}
