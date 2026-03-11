<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Auth;

interface Authentication
{
    public function user(string $guard = null);
}
