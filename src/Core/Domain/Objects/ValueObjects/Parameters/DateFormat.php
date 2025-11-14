<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters;

use Thehouseofel\Kalion\Core\Domain\Concerns\Enums\HasArray;
use Thehouseofel\Kalion\Core\Domain\Contracts\Enums\ArrayableEnum;

enum DateFormat: string implements ArrayableEnum
{
    use HasArray;

    case date_startYear                         = 'Y-m-d';
    case date_startDay                          = 'd-m-Y';
    case date_startYear_slash                   = 'Y/m/d';
    case date_startDay_slash                    = 'd/m/Y';
    case date_startMonthWithoutDay_slash        = 'm/Y';
    case datetime_startYear                     = 'Y-m-d H:i:s';
    case datetime_startYear_withoutSeconds      = 'Y-m-d H:i';
    case datetime_startDay_slash                = 'd/m/Y H:i:s';
    case datetime_startDay_slash_withoutSeconds = 'd/m/Y H:i';
    case datetime_timestamp                     = 'Y-m-d H:i:s.u';
    case datetime_eloquent_timestamps           = 'Y-m-d\TH:i:s.u\Z';
    case time                                   = 'H:i:s';
}
