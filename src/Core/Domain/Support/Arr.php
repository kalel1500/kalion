<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support;

use Illuminate\Support\Traits\Macroable;

class Arr
{
    use Macroable;

    public static function diffAssocDeep(array $array1, array $array2): array
    {
        $difference = [];

        foreach ($array1 as $key => $value) {
            // 1. Si el valor es un array, comparamos recursivamente
            if (is_array($value)) {
                if (! isset($array2[$key]) || ! is_array($array2[$key])) {
                    $difference[$key] = $value;
                    continue;
                }

                $newDiff = static::diffAssocDeep($value, $array2[$key]);
                if (! empty($newDiff)) {
                    $difference[$key] = $newDiff;
                }
                continue;
            }

            // 2. Si es un valor plano, comparamos llave y valor estricto
            if (! array_key_exists($key, $array2) || $array2[$key] !== $value) {
                $difference[$key] = $value;
            }
        }

        return $difference;
    }

    public static function transformIfPresent(array $array, string|array $keys, callable $callback): array
    {
        foreach ((array)$keys as $key) {
            if (array_key_exists($key, $array) && $array[$key] !== null) {
                $array[$key] = $callback($array[$key]);
            }
        }

        return $array;
    }
}
