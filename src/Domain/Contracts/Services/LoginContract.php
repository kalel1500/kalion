<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Services;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
interface LoginContract
{
    public function view(?Request $request = null): View;
    public function login(Request $request): RedirectResponse;
    public function logout(Request $request): RedirectResponse;
}
