<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Contracts;

class AbstractEnumVo extends AbstractBaseEnumVo
{
    protected bool $nullable = false;

    public function value(): string
    {
        return $this->value;
    }
}
