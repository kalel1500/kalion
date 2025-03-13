<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Traits;

trait Instantiable
{
    public static function new(...$args): static
    {
        return new static();
    }
}
