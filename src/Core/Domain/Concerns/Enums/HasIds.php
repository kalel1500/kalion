<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Concerns\Enums;

trait HasIds
{
    /**
     * Obtener el ID asociado a un valor del enum
     */
    public function getId(): int
    {
        return static::ids()[$this->value];
    }

    public static function fromId(int $id): static
    {
        $value = array_search($id, static::ids(), true);
        if ($value === false) {
            $message = sprintf('"%s" is not a valid backing value for enum %s', $id, static::class);
            throw new \ValueError($message);
        }
        return static::from($value);
    }

    public static function tryFromId($id): ?static
    {
        if (is_null($id)) return null;

        $value = array_search((int)$id, static::ids(), true);

        if ($value === false) return null;

        return static::from($value);
    }

    /**
     * Convertir todos los valores del enum en un array de strings
     */
    public static function toArray(): array
    {
        return array_map(fn($case) => $case->value, static::cases());
    }
}
