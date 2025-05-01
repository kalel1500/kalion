<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Config\Redirect;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
abstract class Redirector
{
    /**
     * The callback that should be used to generate the redirect path.
     *
     * @var callable|null
     */
    protected static $redirectToCallback;

    /**
     * Get the path the user should be redirected to
     */
    abstract public static function redirectTo(Request $request = null): ?string;

    /**
     * Get the default URI the user should be redirected to
     */
    protected static function defaultRedirectUri(): string
    {
        foreach (['dashboard', 'home'] as $uri) {
            if (Route::has($uri)) {
                return route($uri);
            }
        }

        $routes = Route::getRoutes()->get('GET');

        foreach (['dashboard', 'home'] as $uri) {
            if (isset($routes[$uri])) {
                return '/'.$uri;
            }
        }

        return '/';
    }

    /**
     * Specify the callback that should be used to generate the redirect path.
     */
    public static function redirectUsing(callable $redirectToCallback): void
    {
        static::$redirectToCallback = $redirectToCallback;
    }
}
