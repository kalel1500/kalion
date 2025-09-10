<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters;

use Thehouseofel\Kalion\Domain\Contracts\Enums\Translatable;
use Thehouseofel\Kalion\Domain\Concerns\Enums\HasTranslations;

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
