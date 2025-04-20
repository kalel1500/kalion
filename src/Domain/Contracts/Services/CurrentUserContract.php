<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Services;

interface CurrentUserContract
{
    public function entity(string $guard = null);
}
