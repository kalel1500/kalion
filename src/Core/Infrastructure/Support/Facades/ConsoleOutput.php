<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Symfony\Component\Console\Output\OutputInterface getOutput()
 * @method static void setOutput(\Symfony\Component\Console\Output\OutputInterface $output)
 * @method static void info(string $message)
 * @method static void error(string $message)
 * @method static void warn(string $message)
 * @method static void line(string $message)
 *
 * @see \Thehouseofel\Kalion\Core\Infrastructure\Utilities\Output\ConsoleOutputRelay
 */
class ConsoleOutput extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'kalion.consoleOutputRelay';
    }
}
