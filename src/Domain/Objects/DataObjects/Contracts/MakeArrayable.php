<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Contracts;

interface MakeArrayable
{
    public function toMakeArray(): array;
}
