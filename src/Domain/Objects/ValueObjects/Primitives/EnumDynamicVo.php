<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Contracts\ContractDynamicEnumVo;

class EnumDynamicVo extends ContractDynamicEnumVo
{
    protected bool $nullable = false;

    public function value(): string
    {
        return $this->value;
    }
}
