<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Services\Auth;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
interface CurrentUser
{
    public function entity(string $guard = null);
}
