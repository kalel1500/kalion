<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Thehouseofel\Kalion\Domain\Contracts\Services\CurrentUserContract;
use Thehouseofel\Kalion\Domain\Contracts\Services\LoginContract;

class AuthManager
{
    public function __construct(
        protected CurrentUserContract $currentUser,
        protected LoginContract $login,
    )
    {
    }

    public function user(string $guard = null)
    {
        return $this->currentUser->entity($guard);
    }

    public function viewLogin(Request $request = null): View
    {
        return $this->login->view($request);
    }

    public function login(Request $request): RedirectResponse
    {
        return $this->login->login($request);
    }

    public function logout(Request $request): RedirectResponse
    {
        return $this->login->logout($request);
    }
}
