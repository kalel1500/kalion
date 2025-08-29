<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects;

use Illuminate\Contracts\Support\Jsonable;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionUnionType;
use Thehouseofel\Kalion\Domain\Contracts\Arrayable;
use Thehouseofel\Kalion\Domain\Contracts\BuildArrayable;
use Thehouseofel\Kalion\Domain\Exceptions\AppException;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\ArrayVo;

abstract class AbstractDataTransferObject implements Arrayable, BuildArrayable, Jsonable
{
    protected const REFLECTION_ACTIVE = false;

    private static array $reflectionCache = [];

    private function getValue($value)
    {
        return ($value instanceof AbstractValueObject) ? $value->value() : $value;
    }

    private function toArrayVisible(): array
    {
        $coll = [];
        foreach ($this as $clave => $valor) {
            $coll[$clave] = $this->getValue($valor);
        }
        return object_to_array($coll);
    }

    public function toArray(): array
    {
        return $this->toArrayVisible();
    }

    public function toArrayForBuild(): array
    {
        return $this->toArrayVisible();
    }

    public function toObject(): object|array
    {
        return array_to_object($this->toArrayVisible());
    }

    public function toArrayVo(): ArrayVo
    {
        return ArrayVo::new($this->toArray());
    }

    public static function fromArray(?array $data): static|null
    {
        if (is_null($data)) return null;
        return static::make($data);
    }

    public static function fromJson(?string $data): static|null
    {
        if (is_null($data)) return null;
        return static::fromArray(json_decode($data, true));
    }

    protected static function make(array $data): static
    {
        if (!static::REFLECTION_ACTIVE) {
            return new static(...array_values($data));
        }

        $className = static::class;

        // Usamos cache para evitar repetir la reflexión
        if (!isset(self::$reflectionCache[$className])) {
            $reflection  = new ReflectionClass($className);
            $constructor = $reflection->getConstructor();

            if (!$constructor) {
                throw new AppException("The " . static::class . " class has no constructor.");
            }

            self::$reflectionCache[$className] = $constructor->getParameters();
        }

        $parameters = self::$reflectionCache[$className];
        $args       = [];

        foreach ($parameters as $param) {
            /** @var string $paramName */
            /** @var ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType|null $paramType */
            $paramName = $param->getName();
            $paramType = $param->getType();

            if (is_null($paramType)) {
                throw new AppException("The \$$paramName parameter in " . static::class . " does not have a defined type.");
            }

            if ($paramType instanceof ReflectionIntersectionType) {
                throw new AppException("Reflection cannot be used on DTOs when the constructor uses IntersectionTypes.");
            }

            if ($paramType instanceof ReflectionUnionType) {
                throw new AppException("Reflection cannot be used on DTOs when the constructor uses UnionTypes.");
            }

            $typeName = $paramType->getName();
            $value    = $data[$paramName] ?? null;

            // Si el valor es un primitivo o ya recibimos la instancia de la clase, pasamos el valor directamente
            if (! class_exists($typeName) || ($value instanceof $typeName)) {
                $args[] = $value;
                continue;
            }

            $method = match (true) {
                is_a($typeName, class: \BackedEnum::class, allow_string: true)          => 'from',
                is_a($typeName, class: AbstractValueObject::class, allow_string: true)  => 'new',
                default                                                                 => 'fromArray',
            };

            $args[] = $typeName::$method($value);
        }

        return new static(...$args);
    }

    public function toJson($options = 0): false|string
    {
        return json_encode($this->toArray(), $options);
    }

    public function __toString()
    {
        return $this->toJson();
    }
}
