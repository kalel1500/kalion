<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Services\Auth\Contracts;

interface Authentication
{
    public function user(string $guard = null);
}
