<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support\Internal;

use JsonException;

/**
 * @internal This class is intended for internal package usage only.
 */
final class Serialization
{
    public static function jsonToArray(mixed $object): ?array
    {
        try {
            $decoded = json_decode(
                json       : json_encode($object, JSON_THROW_ON_ERROR),
                associative: true,
                flags      : JSON_THROW_ON_ERROR,
            );

            return is_array($decoded) ? $decoded : null;
        } catch (JsonException) {
            return null;
        }
    }

    public static function jsonToObject(mixed $object): ?object
    {
        try {
            $decoded = json_decode(
                json       : json_encode($object, JSON_THROW_ON_ERROR),
                associative: false,
                flags      : JSON_THROW_ON_ERROR,
            );

            return is_object($decoded) ? $decoded : null;
        } catch (JsonException) {
            return null;
        }
    }
}
