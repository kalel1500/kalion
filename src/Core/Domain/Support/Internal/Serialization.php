<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support\Internal;

/**
 * @internal This class is intended for internal package usage only.
 */
final class Serialization
{
    public static function jsonToArray(mixed $object): array|object|null
    {
        $string = json_encode($object);
        if (! $string) {
            return null;
        }
        return json_decode($string, true);
    }

    public static function jsonToObject(mixed $object): array|object|null
    {
        $string = json_encode($object);
        if (! $string) {
            return null;
        }
        return json_decode($string);
    }
}
