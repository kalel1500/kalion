<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Services\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Thehouseofel\Kalion\Core\Infrastructure\Services\Auth\Contracts\AuthenticationFlow;
use Thehouseofel\Kalion\Core\Infrastructure\Services\Auth\Contracts\Login;
use Thehouseofel\Kalion\Core\Infrastructure\Services\Auth\Contracts\PasswordReset;
use Thehouseofel\Kalion\Core\Infrastructure\Services\Auth\Contracts\Register;

class AuthenticationFlowService implements AuthenticationFlow
{
    public function __construct(
        protected Login         $login,
        protected Register      $register,
        protected PasswordReset $passwordReset,
    )
    {
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
