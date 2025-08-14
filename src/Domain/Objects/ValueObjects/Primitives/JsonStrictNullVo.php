<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Contracts\AbstractJsonVo;

class JsonStrictNullVo extends AbstractJsonVo
{
    protected bool $nullable                = true;
    protected bool $allowStringInformatable = false;
}
