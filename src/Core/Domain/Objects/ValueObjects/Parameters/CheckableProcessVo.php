<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters;

use Thehouseofel\Kalion\Core\Domain\Concerns\Enums\HasTranslations;
use Thehouseofel\Kalion\Core\Domain\Contracts\Enums\Translatable;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
enum CheckableProcessVo: string implements Translatable
{
    use HasTranslations;

    case queue  = 'queue';
    case reverb = 'reverb';

    public static function translations(): array
    {
        return [
            self::queue->value  => 'queue:work',
            self::reverb->value => 'reverb:start',
        ];
    }
}
