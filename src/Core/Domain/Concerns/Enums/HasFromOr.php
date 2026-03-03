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
        return static::tryFrom($value) ?? static::convertDefault($default, false);
    }

    public static function tryFromOr($value, $default): ?static
    {
        return static::tryFrom($value) ?? static::convertDefault($default, true);
    }

    private static function convertDefault($default, bool $useTry): ?static
    {
        if ($default instanceof static || ($default === null && $useTry)) {
            return $default;
        }

        return $useTry ? static::tryFrom($default) : static::from($default);
    }
}
