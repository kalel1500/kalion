<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support;

final class Os
{
    public static function isWindows(): bool
    {
        return PHP_OS_FAMILY === 'Windows';
    }
}
