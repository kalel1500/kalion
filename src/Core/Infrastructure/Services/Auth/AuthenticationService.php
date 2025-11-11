<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Thehouseofel\Kalion\Infrastructure\Services\Auth\Contracts\Authentication;
use Thehouseofel\Kalion\Infrastructure\Services\Auth\Contracts\CurrentUser;
use Thehouseofel\Kalion\Infrastructure\Services\Auth\Contracts\Login;
use Thehouseofel\Kalion\Infrastructure\Services\Auth\Contracts\PasswordReset;
use Thehouseofel\Kalion\Infrastructure\Services\Auth\Contracts\Register;

class AuthenticationService implements Authentication
{
    public function __construct(
        protected CurrentUser   $currentUser,
        protected Login         $login,
        protected Register      $register,
        protected PasswordReset $passwordReset,
    )
    {
    }

    /*----- CurrentUserContract -----*/

    public function user(string $guard = null)
    {
        return $this->currentUser->userEntity($guard);
    }

    /*----- LoginContract -----*/

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

    /*----- RegisterContract -----*/

    public function viewRegister(Request $request = null): View
    {
        return $this->register->view($request);
    }

    public function register(Request $request): RedirectResponse
    {
        return $this->register->register($request);
    }

    /*----- RegisterContract -----*/

    public function viewPasswordReset(Request $request = null): View
    {
        return $this->passwordReset->view($request);
    }

    public function reset(Request $request): RedirectResponse
    {
        return $this->passwordReset->reset($request);
    }
}
