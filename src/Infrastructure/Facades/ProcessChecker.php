<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Thehouseofel\Kalion\Infrastructure\Services\ProcessChecker withCache()
 * @method static \Thehouseofel\Kalion\Infrastructure\Services\ProcessChecker withoutCache()
 * @method static bool isRunning(string $processName)
 * @method static void assert(string $processName, string $errorMessage = null)
 * @method static bool isRunningQueue()
 * @method static void assertQueue(string $errorMessage = null)
 * @method static bool isRunningReverb()
 * @method static void assertReverb(string $errorMessage = null)
 *
 * @see \Thehouseofel\Kalion\Infrastructure\Services\ProcessChecker
 */
final class ProcessChecker extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'thehouseofel.kalion.processChecker';
    }
}
