<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects;

use Illuminate\Contracts\Support\Jsonable;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionUnionType;
use Thehouseofel\Kalion\Domain\Contracts\ArrayConvertible;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Attributes\DisableReflection;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Contracts\MakeParamsArrayable;
use Thehouseofel\Kalion\Domain\Exceptions\ReflectionException;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\ArrayVo;

abstract class AbstractDataTransferObject implements ArrayConvertible, MakeParamsArrayable, Jsonable
{
    private static array $reflectionDisabled = [];
    private static array $reflectionCache = [];

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

            $params = [];

            foreach ($constructor->getParameters() as $param) {
                $name = $param->getName();
                $type = $param->getType();

                // Intersection type → no permitido
                if ($type instanceof ReflectionIntersectionType) {
                    throw ReflectionException::intersectionTypeNotSupported($name, $className, '__construct');
                }

                // Union type → no permitido
                if ($type instanceof ReflectionUnionType) {
                    throw ReflectionException::unionTypeNotSupported($name, $className, '__construct');
                }

                // Named type (Class)
                if ($type instanceof \ReflectionNamedType) {
                    $params[] = [
                        'name'  => $name,
                        'class' => $type->isBuiltin() ? null : $type->getName(),
                    ];
                    continue;
                }

                // Sin tipo
                $params[] = [
                    'name'  => $name,
                    'class' => null,
                ];
            }

            $newParams = [];
            foreach ($params as $meta) {
                $class = $meta['class'];

                $classIsNull = $class === null;
                $isEnum      = !$classIsNull && is_a($class, class: \BackedEnum::class, allow_string: true);
                $isVo        = !$classIsNull && is_a($class, class: AbstractValueObject::class, allow_string: true);
                $isArray     = !$classIsNull && is_a($class, class: ArrayConvertible::class, allow_string: true);

                $makeMethod = match (true) {
                    $classIsNull => null,
                    $isEnum      => 'from',
                    $isVo        => 'new',
                    $isArray     => 'fromArray',
                    default  => throw ReflectionException::unexpectedTypeInDtoConstructor($className, $meta['name']),
                };

                $propsMethod = match (true) {
                    $isVo    => 'value',
                    $isArray => 'toArray',
                    default  => null,
                };

                $newParams[] = [
                    ...$meta,
                    'makeMethod' => $makeMethod,
                    'propsMethod' => $propsMethod,
                    'propsIsEnum' => $isEnum,
                ];
            }

            self::$reflectionCache[$className] = $newParams;
        }

        return self::$reflectionCache[$className];
    }

    private static function isReflectionDisabled(): bool
    {
        $className = static::class;

        if (!isset(self::$reflectionDisabled[$className])) {
            $reflection                           = new ReflectionClass($className); // REFLECTION - cached
            $attributes                           = $reflection->getAttributes(DisableReflection::class);
            self::$reflectionDisabled[$className] = !empty($attributes);
        }

        return self::$reflectionDisabled[$className];
    }

    protected static function make(array $data): static
    {
        if (self::isReflectionDisabled()) {
            return new static(...array_values($data));
        }

        $args = [];

        foreach (self::getConstructorParams() as $key => $meta) {
            $paramName = arr_is_assoc($data) ? $meta['name'] : $key;
            $class  = $meta['class'];
            $value     = $data[$paramName] ?? null;

            $method = $meta['makeMethod'];
            $value = match (true) {
                $method === null || ($value instanceof $class)  => $value,
                default                                         => $class::$method($value),
            };

            $args[] = $value;
        }

        return new static(...$args);
    }

    protected function props(): array
    {
        $coll = [];
        foreach ($this as $clave => $valor) {
            $coll[$clave] = ($valor instanceof AbstractValueObject) ? $valor->value() : $valor;
        }
        return object_to_array($coll);
    }

    /**
     * @template T of array|null
     * @param T $data
     * @return (T is null ? null : static)
     */
    public static function fromArray(?array $data): ?static
    {
        if (is_null($data)) return null;
        return static::make($data);
    }

    public static function fromJson(?string $data): static|null
    {
        if (is_null($data)) return null;
        return static::fromArray(json_decode($data, true));
    }

    public function toArray(): array
    {
        return $this->props();
    }

    public function toMakeParams(): array
    {
        return $this->props();
    }

    public function toArrayVo(): ArrayVo
    {
        return ArrayVo::new($this->toArray());
    }

    public function toObject(): object|array
    {
        return array_to_object($this->toArray());
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
