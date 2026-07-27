<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support;

use Illuminate\Support\Traits\Macroable;

class Arr
{
    use Macroable;

    public static function transformIfPresent(array $array, string|array $keys, callable $callback): array
    {
        foreach ((array) $keys as $key) {
            if (array_key_exists($key, $array) && $array[$key] !== null) {
                $array[$key] = $callback($array[$key]);
            }
        }

        return $array;
    }
}
