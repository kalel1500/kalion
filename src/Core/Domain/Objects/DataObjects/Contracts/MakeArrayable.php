<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Contracts;

interface MakeArrayable
{
    public function toMakeArray(): array;
}
