<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Auth\Contracts;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
interface Authentication
{
    public function user(string $guard = null);
    public function viewLogin(Request $request = null): View;
    public function login(Request $request): RedirectResponse;
    public function logout(Request $request): RedirectResponse;
    public function viewRegister(Request $request = null): View;
    public function register(Request $request): RedirectResponse;
    public function viewPasswordReset(Request $request = null): View;
    public function reset(Request $request): RedirectResponse;
}
