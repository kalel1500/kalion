<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
enum JsonMethodVo: string
{
    case arrayValue   = 'arrayValue';
    case objectValue  = 'objectValue';
    case encodedValue = 'encodedValue';
}
