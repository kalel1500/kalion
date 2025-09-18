<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects;

use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionUnionType;
use Thehouseofel\Kalion\Domain\Contracts\ArrayConvertible;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Attributes\DisableReflection;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Contracts\MakeParamsArrayable;
use Thehouseofel\Kalion\Domain\Exceptions\ReflectionException;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\ArrayVo;

abstract class AbstractDataTransferObject implements ArrayConvertible, MakeParamsArrayable, Jsonable, JsonSerializable
{
    private static array $reflectionDisabled = [];
    private static array $reflectionCache = [];

    private static function getParamType(\ReflectionParameter|\ReflectionProperty $param): array
    {
        $className = static::class;
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
            return [
                'name'  => $name,
                'class' => $type->isBuiltin() ? null : $type->getName(),
            ];
        }

        // Sin tipo
        return [
            'name'  => $name,
            'class' => null,
        ];
    }

    private static function getParamMeta(array $meta): array
    {
        $className = static::class;
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

        return [
            ...$meta,
            'makeMethod' => $makeMethod,
            'propsMethod' => $propsMethod,
            'propsIsEnum' => $isEnum,
        ];
    }

    private static function resolveConstructorParams(): array
    {
        $className = static::class;

        // Cacheamos ya los parámetros procesados
        if (!isset(self::$reflectionCache[$className])) {
            $reflection  = new ReflectionClass($className); // REFLECTION - cached
            $constructor = $reflection->getConstructor();

            if (!$constructor) {
                throw ReflectionException::constructorMissing(static::class);
            }

            $paramsMake = [];
            $paramsProps = [];

            // Make
            foreach ($constructor->getParameters() as $param) {
                $paramsMake[] = self::getParamType($param);
            }

            // Props
            foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $param) {
                $paramsProps[] = self::getParamType($param);
            }

            $newParamsMake = [];
            $newParamsProps = [];
            foreach ($paramsMake as $meta) {
                $newParamsMake[] = self::getParamMeta($meta);
            }
            foreach ($paramsProps as $meta) {
                $newParamsProps[] = self::getParamMeta($meta);
            }

            self::$reflectionCache[$className] = [
                'make' => $newParamsMake,
                'props' => $newParamsProps,
            ];
        }

        return self::$reflectionCache[$className];
    }

    private static function reflectionDisabledData(): array
    {
        $className = static::class;

        if (!isset(self::$reflectionDisabled[$className])) {
            $reflection                           = new ReflectionClass($className); // REFLECTION - cached
            $attributes                           = $reflection->getAttributes(DisableReflection::class);
            self::$reflectionDisabled[$className] = [
                'isDisabled' => !empty($attributes),
                'useJsonSerialization' => !empty($attributes) && $attributes[0]->newInstance()->useJsonSerialization
            ];
        }

        return self::$reflectionDisabled[$className];
    }

    protected static function make(array $data): static
    {
        if (self::reflectionDisabledData()['isDisabled']) {
            return new static(...array_values($data));
        }

        $args = [];

        $params = self::resolveConstructorParams()['make'];
        foreach ($params as $key => $meta) {
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
        $disabled = self::reflectionDisabledData();
        if ($disabled['isDisabled']) {
            if ($disabled['useJsonSerialization']) {
                $props = [];
                foreach ($this as $key => $value) {
                    $props[$key] = $value;
                }
                return legacy_json_to_array($props);
            }

            return throw ReflectionException::disabledReflectionInDto(static::class);
        }

        $props = [];
        $params = self::resolveConstructorParams()['props'];
        foreach ($params as $meta) {
            $name   = $meta['name'];
            $method = $meta['propsMethod'];
            $isEnum = $meta['propsIsEnum'];
            $value  = $this->{$name};

            $value = match (true) {
                $isEnum          => $value->value,
                $method === null => $value,
                default          => $value->{$method}($value),
            };

            $props[$name] = $value;
        }

        return $props;
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
        return legacy_json_to_object($this->toArray());
    }

    public function toJson($options = 0): false|string
    {
        return json_encode($this->toArray(), $options);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString()
    {
        return $this->toJson();
    }
}
