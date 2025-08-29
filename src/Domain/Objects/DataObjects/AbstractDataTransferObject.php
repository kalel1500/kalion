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

        // Cacheamos ya los parámetros procesados
        if (!isset(self::$reflectionCache[$className])) {
            $reflection  = new ReflectionClass($className); // REFLECTION - cached
            $constructor = $reflection->getConstructor();

            if (!$constructor) {
                throw new AppException("The " . static::class . " class has no constructor.");
            }

            $paramsMeta = [];

            foreach ($constructor->getParameters() as $param) {
                $paramName = $param->getName();
                $paramType = $param->getType();

                if (is_null($paramType)) {
                    throw new AppException("The \$$paramName parameter in $className does not have a defined type.");
                }

                if ($paramType instanceof ReflectionIntersectionType) {
                    throw new AppException("Reflection cannot be used on DTOs when the constructor uses IntersectionTypes.");
                }

                if ($paramType instanceof ReflectionUnionType) {
                    throw new AppException("Union types are not supported in AbstractDataTransferObject. Please override make() in your DTO to handle them.");
                }

                $typeName = $paramType->getName();

                $paramsMeta[] = [
                    'name'     => $paramName,
                    'type'     => $typeName,
                    'isClass'  => class_exists($typeName),
                    'isEnum'   => is_a($typeName, \BackedEnum::class, true),
                    'isVO'     => is_a($typeName, AbstractValueObject::class, true),
                ];
            }

            self::$reflectionCache[$className] = $paramsMeta;
        }

        $args = [];

        foreach (self::$reflectionCache[$className] as $meta) {
            $paramName = $meta['name'];
            $typeName  = $meta['type'];
            $value     = $data[$paramName] ?? null;

            // Si es primitivo o ya recibimos la instancia de la clase, lo usamos directamente
            if (!$meta['isClass'] || ($value instanceof $typeName)) {
                $args[] = $value;
                continue;
            }

            $method = match (true) {
                $meta['isEnum'] => 'from',
                $meta['isVO']   => 'new',
                default         => 'fromArray',
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
