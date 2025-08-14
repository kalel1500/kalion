<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Contracts\AbstractJsonVo;

class JsonStrictVo extends AbstractJsonVo
{
    protected bool $nullable                = false;
    protected bool $allowStringInformatable = false;
}
