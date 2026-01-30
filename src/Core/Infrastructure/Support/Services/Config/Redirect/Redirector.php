<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Config\Redirect;

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
     * Specify the callback that should be used to generate the redirect path.
     */
    public static function redirectUsing(callable $redirectToCallback): void
    {
        static::$redirectToCallback = $redirectToCallback;
    }

    /**
     * Get the path the user should be redirected to
     */
    public function redirectTo(Request $request = null): ?string
    {
        $to = static::$redirectToCallback
            ? call_user_func(static::$redirectToCallback, $request)
            : ($this->getConfigPath() ?: $this->defaultRedirectUri());

        return $this->isValidUrl($to)
            ? $to
            : url($to);
    }

    /**
     * Get the configured path the user should be redirected to
     */
    abstract protected function getConfigPath(): ?string;

    /**
     * Get the default URI the user should be redirected to
     */
    protected function defaultRedirectUri(): string
    {
        foreach (['dashboard', 'home', 'welcome'] as $uri) {
            if (Route::has($uri)) {
                return route($uri);
            }
        }

        $routes = Route::getRoutes()->get('GET');

        foreach (['dashboard', 'home', 'welcome'] as $uri) {
            if (isset($routes[$uri])) {
                return '/' . $uri;
            }
        }

        return '/';
    }

    /**
     * Determine if the given path is a valid URL.
     */
    protected function isValidUrl(string $path): bool
    {
        if (! preg_match('~^(#|//|https?://|(mailto|tel|sms):)~', $path)) {
            return filter_var($path, FILTER_VALIDATE_URL) !== false;
        }

        return true;
    }
}
