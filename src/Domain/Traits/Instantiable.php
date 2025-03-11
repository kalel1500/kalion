<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Traits;

trait Instantiable
{
    /**
     * @param ...$args
     * @return static
     */
    public static function new(...$args)
    {
        return new static();
    }
}
