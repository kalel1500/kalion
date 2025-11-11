<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
enum ProcessStatusKeysVo: string
{
    case queueDisabled  = 'queue_disabled';
    case reverbDisabled = 'reverb_disabled';
}
