<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters;

/**
 * @internal This class is intended for internal package usage only.
 */
enum ProcessStatusKeysVo: string
{
    case queueDisabled  = 'queue_disabled';
    case reverbDisabled = 'reverb_disabled';
}
