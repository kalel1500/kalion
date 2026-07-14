<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getRegistry()
 * @method static array getOrderedIdentifiers()
 * @method static array getScanPackages()
 * @method static void setPriority(array $priority)
 * @method static void override(array $overrides, string $identifier)
 * @method static void apply()
 * @method static void afterApply(callable $callback)
 * @method static void registerPackagesToScanJobs(string|array $packages)
 * @method static void redirectTo(callable|string|null $defaultPath = null, callable|string|null $afterLogin = null)
 */
class KalionConfig extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'kalion.config';
    }
}

