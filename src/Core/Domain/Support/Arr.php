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

    public static function replaceInKeys(array $data, string $search, string $replace): array
    {
        return array_combine(
            str_replace($search, $replace, array_keys($data)),
            array_values($data)
        );
    }

    public static function validEmails(array $emails, bool $strict = false, bool $dns = false): array
    {
        return array_values(array_filter(
            array_map('trim', $emails),
            fn (string $email): bool => $strict
                ? Str::isValidEmail($email, $dns)
                : filter_var($email, FILTER_VALIDATE_EMAIL) !== false
        ));
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
