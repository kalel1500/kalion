<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support;

use Illuminate\Support\Traits\Macroable;

class Path
{
    use Macroable;

    public static function normalize(string $path): string
    {
        return str_replace(
            ['/', '\\'],
            DIRECTORY_SEPARATOR,
            $path,
        );
    }
}
