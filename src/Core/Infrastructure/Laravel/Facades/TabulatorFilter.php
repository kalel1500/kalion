<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Thehouseofel\Kalion\Core\Infrastructure\Support\Filters\TabulatorFilterManager driver(string $driver)
 * @method static mixed filter(mixed $query, ?array $filters, ?array $sorters = null)
 *
 * @see \Thehouseofel\Kalion\Core\Infrastructure\Support\Filters\TabulatorFilterManager
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
