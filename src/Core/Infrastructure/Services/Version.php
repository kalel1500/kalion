<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Services;

final class Version
{
    /**
     * Determinar si la version actual de PHP es igual o mayor a la 7.4
     */
    public static function phpMin74(): bool
    {
        return version_compare(PHP_VERSION, '7.4', '>=');
    }

    /**
     * Determinar si la version de Laravel instalada es igual o mayor a la 12
     */
    public static function laravelMin12(): bool
    {
        return version_compare(app()->version(), '12', '>=');
    }
}
