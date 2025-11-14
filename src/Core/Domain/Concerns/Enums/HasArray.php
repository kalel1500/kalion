<?php

namespace Thehouseofel\Kalion\Core\Domain\Concerns\Enums;

trait HasArray
{
    /**
     * Convertir todos los valores del enum en un array de strings
     */
    public static function toArray(bool $getKeys = false): array
    {
        return array_map(fn(\BackedEnum $case) => $getKeys ? $case->name : $case->value, static::cases());
    }
}
