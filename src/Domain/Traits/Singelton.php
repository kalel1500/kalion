<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Traits;

trait Singelton
{
    private static $instance;

    public static function instance()
    {
        if (!static::$instance instanceof static) {
            static::$instance = new static();
        }

        return static::$instance;
    }

}
