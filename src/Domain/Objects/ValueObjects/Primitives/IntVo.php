<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Contracts\ContractIntVo;

class IntVo extends ContractIntVo
{
    protected bool $nullable = false;

    public function __construct(int $value)
    {
        parent::__construct($value);
    }

    public function value(): int
    {
        return $this->value;
    }
}
