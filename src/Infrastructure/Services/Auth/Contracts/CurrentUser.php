<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Auth\Contracts;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
interface CurrentUser
{
    public function userEntity(string $guard = null);
}
