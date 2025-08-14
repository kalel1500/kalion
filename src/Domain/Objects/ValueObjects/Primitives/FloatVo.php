<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractFloatVo;

class FloatVo extends AbstractFloatVo
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
