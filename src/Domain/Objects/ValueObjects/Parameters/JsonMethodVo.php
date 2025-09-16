<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters;

enum JsonMethodVo: string
{
    case arrayValue   = 'arrayValue';
    case objectValue  = 'objectValue';
    case encodedValue = 'encodedValue';
}
