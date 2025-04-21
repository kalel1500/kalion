<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Auth;

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

    public function authenticate(Request $request): void
    {
        $this->login->authenticate($request);
    }
}
