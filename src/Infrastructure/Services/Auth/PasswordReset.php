<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Thehouseofel\Kalion\Domain\Contracts\Services\PasswordResetContract;
use Thehouseofel\Kalion\Domain\Exceptions\FeatureUnavailableException;

class PasswordReset implements PasswordResetContract
{
    public function view(?Request $request = null): View
    {
        return view(config('kalion.auth.blades.password_reset'));
    }

    public function reset(Request $request): RedirectResponse
    {
        throw new FeatureUnavailableException();
    }
}
