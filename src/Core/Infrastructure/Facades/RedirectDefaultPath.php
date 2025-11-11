<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Facades;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string|null redirectTo(Request $request = null)
 *
 * @see \Thehouseofel\Kalion\Core\Infrastructure\Services\Config\Redirect\RedirectDefaultPath
 */
final class RedirectDefaultPath extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'thehouseofel.kalion.redirectDefaultPath';
    }
}
