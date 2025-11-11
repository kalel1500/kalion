<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Thehouseofel\Kalion\Domain\Exceptions\FeatureUnavailableException;
use Thehouseofel\Kalion\Infrastructure\Services\Auth\Contracts\PasswordReset;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
class PasswordResetService implements PasswordReset
{
    public function view(?Request $request = null): View
    {
        return view(config('kalion.auth.blades.password_reset'));
    }

    public function reset(Request $request): RedirectResponse
    {
        throw FeatureUnavailableException::default();
    }
}
