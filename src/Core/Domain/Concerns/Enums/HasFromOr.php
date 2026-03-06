<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Concerns\Enums;

/**
 * This trait must only be used on Backed Enums.
 *
 * @template T of \BackedEnum
 */
trait HasFromOr
{
    public static function fromOr($value, $default): ?static
    {
        return static::resolveEnum($value, $default, false);
    }

    public static function tryFromOr($value, $default): ?static
    {
        return static::resolveEnum($value, $default, true);
    }

    private static function resolveEnum($value, $default, bool $useTry): ?static
    {
        if ($value !== null && ($enum = static::tryFrom($value))) {
            return $enum;
        }

        return static::convertDefault($default, $useTry);
    }

    private static function convertDefault($default, bool $useTry): ?static
    {
        if ($default instanceof static || ($default === null && $useTry)) {
            return $default;
        }

        return $useTry ? static::tryFrom($default) : static::from($default);
    }
}
