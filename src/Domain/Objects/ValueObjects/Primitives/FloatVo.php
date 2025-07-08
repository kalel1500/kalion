<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Contracts\ContractFloatVo;

class FloatVo extends ContractFloatVo
{
    protected bool $nullable = false;

    public function __construct(float $value)
    {
        parent::__construct($value);
    }

    public function value(): float
    {
        return $this->value;
    }
}
