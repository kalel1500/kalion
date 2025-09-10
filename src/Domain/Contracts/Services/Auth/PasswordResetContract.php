<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Services\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
interface PasswordResetContract
{
    public function view(?Request $request = null): View;
    public function reset(Request $request): RedirectResponse;
}
