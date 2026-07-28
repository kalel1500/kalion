<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters;

/**
 * @internal This class is intended for internal package usage only.
 */
enum JsonMethodVo: string
{
    case arrayValue   = 'arrayValue';
    case objectValue  = 'objectValue';
    case encodedValue = 'encodedValue';
}
