<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters;

use Thehouseofel\Kalion\Core\Domain\Concerns\Enums\HasArray;
use Thehouseofel\Kalion\Core\Domain\Contracts\Enums\ArrayableEnum;

enum DateFormat: string implements ArrayableEnum
{
    use HasArray;

    // Fechas con guión (separador por defecto, sin sufijo)
    case date_YMD                 = 'Y-m-d';
    case date_DMY                 = 'd-m-Y';

    // Fechas con barra
    case date_YMD_slash           = 'Y/m/d';
    case date_DMY_slash           = 'd/m/Y';
    case date_MY_slash            = 'm/Y';

    // Datetime con guión
    case datetime_YMD             = 'Y-m-d H:i:s';
    case datetime_YMD_short       = 'Y-m-d H:i';

    // Datetime con barra
    case datetime_DMY_slash       = 'd/m/Y H:i:s';
    case datetime_DMY_slash_short = 'd/m/Y H:i';

    // Datetime especiales
    case datetime_micro           = 'Y-m-d H:i:s.u';
    case datetime_eloquent        = 'Y-m-d\TH:i:s.u\Z';

    // HTML datetime-local
    case html_datetime            = 'Y-m-d\TH:i:s';
    case html_datetime_short      = 'Y-m-d\TH:i';

    // Otros
    case time                     = 'H:i:s';
    case zeros                    = '0000-00-00 00:00:00';
}
