<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Config\Redirect;

use Illuminate\Http\Request;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
class RedirectDefaultPath extends Redirector
{
    protected static $redirectToCallback;

    protected function getConfigPath(): ?string
    {
        return config('kalion.default_path');
    }
}
