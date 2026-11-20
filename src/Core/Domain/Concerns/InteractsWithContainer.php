<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Concerns;

use Illuminate\Container\Container;

/**
 * Allows domain classes to use Laravel services only when they are available,
 * so the domain can also run without booting Laravel (e.g. in plain PHPUnit tests).
 *
 * @internal This trait is intended for internal package usage only.
 */
trait InteractsWithContainer
{
    private static function hasBinding(string $abstract): bool
    {
        return Container::getInstance()->bound($abstract);
    }

    private static function resolveBinding(string $abstract): mixed
    {
        return Container::getInstance()->make($abstract);
    }
}
