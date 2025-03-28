<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Contracts\ContractBoolVo;

class BoolVo extends ContractBoolVo
{
    protected bool $nullable = false;

    public function value(): bool
    {
        return (bool)$this->value;
    }

    public function valueInt(): int
    {
        return (int)$this->value();
    }
}
