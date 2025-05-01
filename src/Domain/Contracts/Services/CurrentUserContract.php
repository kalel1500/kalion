<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Services;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
interface CurrentUserContract
{
    public function entity(string $guard = null);
}
