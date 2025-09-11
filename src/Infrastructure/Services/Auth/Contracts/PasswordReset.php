<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Auth\Contracts;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
interface PasswordReset
{
    public function view(?Request $request = null): View;
    public function reset(Request $request): RedirectResponse;
}
