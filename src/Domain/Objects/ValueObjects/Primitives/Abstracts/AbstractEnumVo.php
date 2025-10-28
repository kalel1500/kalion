<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractBaseEnumVo;

class AbstractEnumVo extends AbstractBaseEnumVo
{
    protected bool $nullable = false;

    public function value(): string
    {
        return $this->value;
    }
}
