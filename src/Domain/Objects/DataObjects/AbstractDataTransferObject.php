<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects;

use Illuminate\Contracts\Support\Jsonable;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionUnionType;
use Thehouseofel\Kalion\Domain\Contracts\Arrayable;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Contracts\BuildArrayable;
use Thehouseofel\Kalion\Domain\Exceptions\ReflectionException;
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

    private static function getConstructorParams(): array
    {
        $className = static::class;

        // Cacheamos ya los parámetros procesados
        if (!isset(self::$reflectionCache[$className])) {
            $reflection  = new ReflectionClass($className); // REFLECTION - cached
            $constructor = $reflection->getConstructor();

            if (!$constructor) {
                throw ReflectionException::constructorMissing(static::class);
            }

            $paramsMeta = [];

            foreach ($constructor->getParameters() as $param) {
                $paramName = $param->getName();
                $paramType = $param->getType();

                if (is_null($paramType)) {
                    throw ReflectionException::typeRequiredOnParam($paramName, $className, '__construct');
                }

                if ($paramType instanceof ReflectionIntersectionType) {
                    throw ReflectionException::intersectionTypeNotSupported($paramName, $className, '__construct');
                }

                if ($paramType instanceof ReflectionUnionType) {
                    throw ReflectionException::unionTypeNotSupported($paramName, $className, '__construct');
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

        return self::$reflectionCache[$className];
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

        $args = [];

        foreach (self::getConstructorParams() as $key => $meta) {
            $paramName = arr_is_assoc($data) ? $meta['name'] : $key;
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
