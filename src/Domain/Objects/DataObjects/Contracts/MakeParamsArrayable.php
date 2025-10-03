<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Contracts;

interface MakeParamsArrayable
{
    public function toMakeArray(): array;
}
