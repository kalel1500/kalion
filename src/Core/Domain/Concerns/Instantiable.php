<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Concerns;

trait Instantiable
{
    public static function new(...$args): static
    {
        return new static();
    }
}
