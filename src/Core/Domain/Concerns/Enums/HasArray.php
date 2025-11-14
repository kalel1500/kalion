<?php

namespace Thehouseofel\Kalion\Core\Domain\Concerns\Enums;

trait HasArray
{
    /**
     * Convertir todos los valores del enum en un array de strings
     */
    public static function toArray(): array
    {
        return array_map(fn($case) => $case->value, static::cases());
    }
}
