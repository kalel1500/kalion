<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Traits;

/**
 * @template TEnum of \BackedEnum
 */
trait WithIdsAndToArray
{
    /**
     * Obtener el ID asociado a un valor del enum
     */
    public function getId(): int {
        return static::values()[$this->value];
    }

    /**
     * @param int $id
     * @return TEnum
     */
    public static function fromId(int $id) {
        $value = array_search($id, static::values(), true);
        if ($value === false) {
            $message = sprintf('"%s" is not a valid backing value for enum %s', $id, static::class);
            throw new \ValueError($message);
        }
        return static::from($value);
    }

    /**
     * Convertir todos los valores del enum en un array de strings
     */
    public static function toArray(): array {
        return array_map(fn($case) => $case->value, static::cases());
    }
}
