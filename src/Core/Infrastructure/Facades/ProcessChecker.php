<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\CheckableProcessVo;

/**
 * @method static \Thehouseofel\Kalion\Core\Infrastructure\Services\ProcessChecker withCache()
 * @method static \Thehouseofel\Kalion\Core\Infrastructure\Services\ProcessChecker withoutCache()
 * @method static bool isRunning(CheckableProcessVo $processName)
 * @method static bool tryIsRunning(CheckableProcessVo $processName)
 * @method static void assert(string $processName, string $errorMessage = null)
 * @method static bool isRunningQueue()
 * @method static bool tryIsRunningQueue()
 * @method static void assertQueue(string $errorMessage = null)
 * @method static bool isRunningReverb()
 * @method static bool tryIsRunningReverb()
 * @method static void assertReverb(string $errorMessage = null)
 *
 * @see \Thehouseofel\Kalion\Core\Infrastructure\Services\ProcessChecker
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
