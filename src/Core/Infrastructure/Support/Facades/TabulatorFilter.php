<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Thehouseofel\Kalion\Core\Infrastructure\Utilities\Filters\TabulatorFilterManager driver(string $driver)
 * @method static filter(mixed $query, ?array $filters, ?array $sorters = null)
 *
 * @see \Thehouseofel\Kalion\Core\Infrastructure\Utilities\Filters\TabulatorFilterManager
 */
class TabulatorFilter extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'kalion.tabulatorFilter';
    }
}
