<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Facades;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string|null redirectTo(Request $request = null)
 *
 * @see \Thehouseofel\Kalion\Infrastructure\Services\Config\Redirect\RedirectAfterLogin
 */
final class RedirectAfterLogin extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'thehouseofel.kalion.redirectAfterLogin';
    }
}
