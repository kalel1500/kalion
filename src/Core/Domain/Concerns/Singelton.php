<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Concerns;

/**
 * @internal This class is intended for internal package usage only.
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
