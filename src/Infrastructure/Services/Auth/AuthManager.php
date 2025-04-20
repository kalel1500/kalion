<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Auth;

use Thehouseofel\Kalion\Domain\Contracts\Services\CurrentUserContract;

class AuthManager
{
    public function __construct(
        protected CurrentUserContract $currentUser,
    )
    {
    }

    public function user(string $guard = null)
    {
        return $this->currentUser->userEntity($guard);
    }
}
