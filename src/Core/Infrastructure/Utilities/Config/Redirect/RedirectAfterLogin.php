<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Config\Redirect;

/**
 * @internal This class is intended for internal package usage only.
 */
class RedirectAfterLogin extends Redirector
{
    protected static $redirectToCallback;

    protected function getConfigPath(): ?string
    {
        return config('kalion.auth.redirect_after_login');
    }
}
