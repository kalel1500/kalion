<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Config\Redirect;

use Illuminate\Http\Request;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
class RedirectAfterLogin extends Redirector
{
    protected static $redirectToCallback;

    public static function redirectTo(Request $request = null): ?string
    {
        return static::$redirectToCallback
            ? call_user_func(static::$redirectToCallback, $request)
            : (config('kalion.auth.redirect_after_login') ?: static::defaultRedirectUri());
    }
}
