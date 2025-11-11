<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Concerns;

trait Instantiable
{
    public static function new(...$args): static
    {
        return new static();
    }
}
