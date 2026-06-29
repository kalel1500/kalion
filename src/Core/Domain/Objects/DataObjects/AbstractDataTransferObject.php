<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects;

use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionUnionType;
use Thehouseofel\Kalion\Core\Domain\Contracts\ArrayConvertible;
use Thehouseofel\Kalion\Core\Domain\Contracts\ArrayResolvable;
use Thehouseofel\Kalion\Core\Domain\Exceptions\KalionReflectionException;
use Thehouseofel\Kalion\Core\Domain\Objects\Attributes\UseMethod;
use Thehouseofel\Kalion\Core\Domain\Objects\Attributes\WithParams;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Attributes\DisableReflection;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Contracts\MakeArrayable;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractDateVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractArrayVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractBoolVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractFloatVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractIntVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractJsonVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractStringVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\ArrayVo;

abstract class AbstractDataTransferObject implements ArrayConvertible, ArrayResolvable, MakeArrayable, Jsonable, JsonSerializable
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

        $typeName   = null;
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
            $typeName   = $type->getName();
            $class      = $type->isBuiltin() ? null : $typeName;
            $allowsNull = $type->allowsNull();
        }

        // Attr: UseMethod
        $useMethodAttr = $param->getAttributes(UseMethod::class);
        $useMethod     = ! empty($useMethodAttr) ? $useMethodAttr[0]->newInstance()->method : null;

        if (! empty($attrs)) {
            /** @var WithParams $attr */
            $attr       = $attrs[0]->newInstance();
            $makeParams = $attr->params;
        }

        // Devolver el array con la información del parámetro
        return [
            'name'       => $name,
            'typeName'   => $typeName,
            'class'      => $class,
            'allowsNull' => $allowsNull,
            'useMethod'  => $useMethod,
            'makeParams' => $makeParams,
        ];
    }

    private static function getParamMeta(array $meta, bool $resolve): array
    {
        $className = static::class;
        $typeName  = $meta['typeName'];
        $class     = $meta['class'];
        $useMethod = $meta['useMethod'];

        $classIsNull       = $class === null;
        $isEnum            = ! $classIsNull && is_a($class, class: \BackedEnum::class, allow_string: true);
        $isVo              = ! $classIsNull && is_a($class, class: AbstractValueObject::class, allow_string: true);
        $isArray           = ! $classIsNull && is_a($class, class: ArrayConvertible::class, allow_string: true);
        $isArrayResolvable = ! $classIsNull && is_a($class, class: ArrayResolvable::class, allow_string: true);

        $makeMethod = match (true) {
            $classIsNull                      => null,
            $useMethod !== null               => $useMethod,
            ($isVo && $resolve)               => 'parse',
            $isEnum || $isVo                  => 'from',
            $isArrayResolvable && $resolve    => 'resolveFromArray',
            $isArray                          => 'fromArray',
            default                           => throw KalionReflectionException::unexpectedTypeInDtoConstructor($className, $meta['name']),
        };

        $propsMethod = match (true) {
            $isArray => 'toArray',
            default  => null,
        };

        $castType = match (true) {
            $classIsNull                                                     => $typeName,
            is_a($class, class: AbstractArrayVo::class, allow_string: true)  => 'array',
            is_a($class, class: AbstractBoolVo::class, allow_string: true)   => 'bool',
            is_a($class, class: AbstractFloatVo::class, allow_string: true)  => 'float',
            is_a($class, class: AbstractIntVo::class, allow_string: true)    => 'int',
            is_a($class, class: AbstractJsonVo::class, allow_string: true)   => 'string',
            is_a($class, class: AbstractStringVo::class, allow_string: true) => 'string',
            default                                                          => null,
        };

        return [
            ...$meta,
            'isEnum'      => $isEnum,
            'isVo'        => $isVo,
            'makeMethod'  => $makeMethod,
            'propsMethod' => $propsMethod,
            'castType'    => $castType,
        ];
    }

    private static function resolveConstructorParams(bool $resolve): array
    {
        $className = static::class;

        $cacheKey = $className . ($resolve ? ':resolve' : '');

        // Cacheamos ya los parámetros procesados
        if (! isset(self::$reflectionCache[$cacheKey])) {
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
                $newParamsMake[] = self::getParamMeta($meta, $resolve);
            }
            foreach ($paramsProps as $meta) {
                $newParamsProps[] = self::getParamMeta($meta, $resolve);
            }

            self::$reflectionCache[$cacheKey] = [
                'make'  => $newParamsMake,
                'props' => $newParamsProps,
            ];
        }

        return self::$reflectionCache[$cacheKey];
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

    protected static function make(array $data, bool $resolve = false): static
    {
        if (self::reflectionDisabledData()['isDisabled']) {
            return new static(...array_values($data));
        }

        $args = [];

        $params = self::resolveConstructorParams($resolve)['make'];
        foreach ($params as $key => $meta) {
            $paramName  = arr_is_assoc($data) ? $meta['name'] : $key;
            $class      = $meta['class'];
            $allowsNull = $meta['allowsNull'];
            $isEnum     = $meta['isEnum'];
            $method     = $meta['makeMethod'];
            $makeParams = $meta['makeParams'] ?? [];
            $castType   = $meta['castType'];
            $value      = $data[$paramName] ?? null;

            if ($resolve && $value !== null && $castType !== null) {
                $value = match ($castType) {
                    'array'  => (array)$value,
                    'bool'   => (bool)$value,
                    'float'  => (float)$value,
                    'int'    => (int)$value,
                    'string' => (string)$value,
                };
            }

            try {
                $value = match (true) {
                    ($allowsNull && $value === null) || $method === null || ($value instanceof $class) => $value,
                    (! $allowsNull && $value === null && $isEnum)                                      => $class::$method(KALION_ENUM_NULL_VALUE),
                    default                                                                            => $class::$method($value, ...$makeParams),
                };
            } catch (\Throwable $th) {
                throw KalionReflectionException::resolveFailedToHydrate(
                    th: $th,
                    expectedClass: AbstractValueObject::class,
                    exception: KalionReflectionException::failedToHydrateValueObject(static::class, $paramName, $class, $value, $th->getMessage())
                );
            }

            $args[] = $value;
        }

        try {
            return new static(...$args);
        } catch (\Throwable $th) {
            throw KalionReflectionException::resolveFailedToHydrate(
                th: $th,
                expectedClass: AbstractDataTransferObject::class,
                exception: KalionReflectionException::failedToHydrateClass(static::class, $th->getMessage())
            );
        }
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
        $params = self::resolveConstructorParams(false)['props'];
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

            if ($isEnum && $value === KALION_ENUM_NULL_VALUE) {
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

    /**
     * @template T of array|null
     * @param T $data
     * @return (T is null ? null : static)
     */
    public static function resolveFromArray(?array $data): ?static
    {
        if (empty($data)) return null;
        return static::make($data, true);
    }

    /**
     * @template T of string|null
     * @param T $data
     * @return (T is null ? null : static)
     */
    public static function fromJson(?string $data): static|null
    {
        if (is_null($data)) return null;
        return static::fromArray(json_decode($data, true));
    }

    public static function tryFromArray($data): ?static
    {
        try {
            return static::fromArray($data);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function tryFromJson($data): ?static
    {
        try {
            return static::fromJson($data);
        } catch (\Throwable) {
            return null;
        }
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
