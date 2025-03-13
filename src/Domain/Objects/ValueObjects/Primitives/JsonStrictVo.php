<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Contracts\ContractJsonVo;

class JsonStrictVo extends ContractJsonVo
{
    protected bool $nullable                = false;
    protected bool $allowStringInformatable = false;
}
