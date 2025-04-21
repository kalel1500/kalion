<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Facades;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * @method static user(string|null $guard = null)
 * @method static View viewLogin(Request $request = null)
 * @method static RedirectResponse login(Request $request)
 * @method static RedirectResponse logout(Request $request)
 *
 * @see \Thehouseofel\Kalion\Infrastructure\Services\Auth\AuthManager
 */
final class Auth extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'authManager';
    }
}
