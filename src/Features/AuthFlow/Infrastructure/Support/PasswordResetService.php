<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Support;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Thehouseofel\Kalion\Core\Domain\Exceptions\FeatureUnavailableException;
use Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Support\Contracts\PasswordReset;

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
