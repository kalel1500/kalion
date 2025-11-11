<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects;

use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionUnionType;
use Thehouseofel\Kalion\Core\Domain\Contracts\ArrayConvertible;
use Thehouseofel\Kalion\Core\Domain\Exceptions\KalionReflectionException;
use Thehouseofel\Kalion\Core\Domain\Objects\Attributes\WithParams;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Attributes\DisableReflection;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Contracts\MakeArrayable;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\ArrayVo;
use Thehouseofel\Kalion\Core\Infrastructure\Services\Kalion;

abstract class AbstractDataTransferObject implements ArrayConvertible, MakeArrayable, Jsonable, JsonSerializable
{
    private static array $reflectionDisabled = [];
    private static array $reflectionCache    = [];

    private static function getParamType(\ReflectionParameter|\ReflectionProperty $param, bool $allowUnionTypes): array
    {
        $className = static::class;
        $name      = $param->getName();
        $type      = $param->getType();
        $attrs     = $param->getAttributes(WithParams::class);

        // Intersection type → no permitido
        if ($type instanceof ReflectionIntersectionType) {
            throw KalionReflectionException::intersectionTypeNotSupported($name, $className, '__construct');
        }

        $class      = null;
        $allowsNull = true;
        $makeParams = null;

        // Union type → no permitido
        if ($type instanceof ReflectionUnionType) {
            if (! $allowUnionTypes) {
                throw KalionReflectionException::unionTypeNotSupported($name, $className, '__construct');
            }

            $type = $type->getTypes()[0];
        }

        // Named type (Class)
        if ($type instanceof \ReflectionNamedType) {
            $class      = $type->isBuiltin() ? null : $type->getName();
            $allowsNull = $type->allowsNull();
        }

        if (! empty($attrs)) {
            /** @var WithParams $attr */
            $attr       = $attrs[0]->newInstance();
            $makeParams = $attr->params;
        }

        // Devolver el array con la información del parámetro
        return [
            'name'       => $name,
            'class'      => $class,
            'allowsNull' => $allowsNull,
            'makeParams' => $makeParams,
        ];
    }

    private static function getParamMeta(array $meta): array
    {
        $className = static::class;
        $class     = $meta['class'];

        $classIsNull = $class === null;
        $isEnum      = ! $classIsNull && is_a($class, class: \BackedEnum::class, allow_string: true);
        $isVo        = ! $classIsNull && is_a($class, class: AbstractValueObject::class, allow_string: true);
        $isArray     = ! $classIsNull && is_a($class, class: ArrayConvertible::class, allow_string: true);

        $makeMethod = match (true) {
            $classIsNull     => null,
            $isEnum || $isVo => 'from',
            $isArray         => 'fromArray',
            default          => throw KalionReflectionException::unexpectedTypeInDtoConstructor($className, $meta['name']),
        };

        $propsMethod = match (true) {
            $isArray => 'toArray',
            default  => null,
        };

        return [
            ...$meta,
            'isEnum'      => $isEnum,
            'isVo'        => $isVo,
            'makeMethod'  => $makeMethod,
            'propsMethod' => $propsMethod,
        ];
    }

    private static function resolveConstructorParams(): array
    {
        $className = static::class;

        // Cacheamos ya los parámetros procesados
        if (! isset(self::$reflectionCache[$className])) {
            $reflection  = new ReflectionClass($className); // REFLECTION - cached
            $constructor = $reflection->getConstructor();

            if (! $constructor) {
                throw KalionReflectionException::constructorMissing(static::class);
            }

            $paramsMake  = [];
            $paramsProps = [];

            // Make
            foreach ($constructor->getParameters() as $param) {
                $paramsMake[] = self::getParamType($param, false);
            }

            // Props
            foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $param) {
                $paramsProps[] = self::getParamType($param, true);
            }

            $newParamsMake  = [];
            $newParamsProps = [];
            foreach ($paramsMake as $meta) {
                $newParamsMake[] = self::getParamMeta($meta);
            }
            foreach ($paramsProps as $meta) {
                $newParamsProps[] = self::getParamMeta($meta);
            }

            self::$reflectionCache[$className] = [
                'make'  => $newParamsMake,
                'props' => $newParamsProps,
            ];
        }

        return self::$reflectionCache[$className];
    }

    private static function reflectionDisabledData(): array
    {
        $className = static::class;

        if (! isset(self::$reflectionDisabled[$className])) {
            $reflection                           = new ReflectionClass($className); // REFLECTION - cached
            $attributes                           = $reflection->getAttributes(DisableReflection::class);
            self::$reflectionDisabled[$className] = [
                'isDisabled'           => ! empty($attributes),
                'useJsonSerialization' => ! empty($attributes) && $attributes[0]->newInstance()->useJsonSerialization
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
            $paramName  = arr_is_assoc($data) ? $meta['name'] : $key;
            $class      = $meta['class'];
            $allowsNull = $meta['allowsNull'];
            $isEnum     = $meta['isEnum'];
            $method     = $meta['makeMethod'];
            $makeParams = $meta['makeParams'] ?? [];
            $value      = $data[$paramName] ?? null;

            try {
                $value = match (true) {
                    ($allowsNull && $value === null) || $method === null || ($value instanceof $class) => $value,
                    (! $allowsNull && $value === null && $isEnum)                                      => $class::$method(Kalion::ENUM_NULL_VALUE),
                    default                                                                            => $class::$method($value, ...$makeParams),
                };
            } catch (\Throwable $th) {
                throw KalionReflectionException::failedToHydrateUsingFromArray(static::class, $paramName, $class, $value, $th->getMessage());
            }

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

            return throw KalionReflectionException::disabledReflectionInDto(static::class);
        }

        $props  = [];
        $params = self::resolveConstructorParams()['props'];
        foreach ($params as $meta) {
            $name   = $meta['name'];
            $isEnum = $meta['isEnum'];
            $isVo   = $meta['isVo'];
            $method = $meta['propsMethod'];
            $value  = $this->{$name};

            $value = match (true) {
                $isEnum || $isVo => $value?->value,
                $method === null => $value,
                default          => $value?->{$method}(),
            };

            if ($isEnum && $value === Kalion::ENUM_NULL_VALUE) {
                $value = null;
            }

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
        if (empty($data)) return null;
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

    public function toMakeArray(): array
    {
        return $this->props();
    }

    public function toArrayVo(): ArrayVo
    {
        return ArrayVo::from($this->toArray());
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
