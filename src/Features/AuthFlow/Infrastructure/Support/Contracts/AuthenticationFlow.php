<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Support\Contracts;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

interface AuthenticationFlow
{
    public function viewLogin(Request $request = null): View;

    public function login(Request $request): RedirectResponse;

    public function logout(Request $request): RedirectResponse;

    public function viewRegister(Request $request = null): View;

    public function register(Request $request): RedirectResponse;

    public function viewPasswordReset(Request $request = null): View;

    public function reset(Request $request): RedirectResponse;
}
