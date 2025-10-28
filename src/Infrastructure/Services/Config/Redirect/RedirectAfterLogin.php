<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Config\Redirect;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
class RedirectAfterLogin extends Redirector
{
    protected static $redirectToCallback;

    protected function getConfigPath(): ?string
    {
        return config('kalion.auth.redirect_after_login');
    }
}
