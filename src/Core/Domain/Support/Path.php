<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support;

use Illuminate\Support\Traits\Macroable;

class Path
{
    use Macroable;

    public static function normalize(string $path): string
    {
        return DIRECTORY_SEPARATOR === '\\'
            ? str_replace('/', '\\', $path)  // Windows
            : str_replace('\\', '/', $path); // Linux/macOS
    }
}
