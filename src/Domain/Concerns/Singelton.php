<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Concerns;

/**
 * @internal This trait is not meant to be used or overwritten outside the package.
 */
trait Singelton
{
    private static $instance;

    public static function instance()
    {
        if (! static::$instance instanceof static) {
            static::$instance = new static();
        }

        return static::$instance;
    }

}
