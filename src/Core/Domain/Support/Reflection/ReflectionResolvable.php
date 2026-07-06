<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support\Reflection;

use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;
use Thehouseofel\Kalion\Core\Domain\Contracts\ArrayConvertible;
use Thehouseofel\Kalion\Core\Domain\Contracts\ArrayResolvable;
use Thehouseofel\Kalion\Core\Domain\Exceptions\KalionReflectionException;
use Thehouseofel\Kalion\Core\Domain\Objects\Attributes\UseMethod;
use Thehouseofel\Kalion\Core\Domain\Objects\Attributes\WithParams;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Attributes\DisableReflection;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractArrayVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractBoolVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractFloatVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractIntVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractJsonVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractStringVo;

trait ReflectionResolvable
{
    private static array $reflectionCache         = [];
    private static array $reflectionDisabledCache = [];
    private static array $reflectionConfigCache   = [];

    abstract protected static function reflectionConfig(): ReflectionConfig;

    private static function config(): ReflectionConfig
    {
        $className = static::class;

        return self::$reflectionConfigCache[$className] ??= static::reflectionConfig();
    }

    private static function getParamMetadata(ReflectionParameter|ReflectionProperty $param, bool $allowUnionTypes, bool $resolve): array
    {
        $className = static::class;
        $paramName = $param->getName();
        $paramType = $param->getType();
        $attrs     = $param->getAttributes(WithParams::class);

        // Intersection type → no permitido
        if ($paramType instanceof ReflectionIntersectionType) {
            throw KalionReflectionException::intersectionTypeNotSupported($paramName, $className, '__construct');
        }

        $typeName   = null;
        $class      = null;
        $allowsNull = true;
        $makeParams = null;
        $isId       = false;

        // Union type (ej. [ent => IdVo|IdNullVo, dto => in props])
        if ($paramType instanceof ReflectionUnionType) {
            [$typeName, $class, $allowsNull, $isId] = self::resolveUnionType(
                paramType      : $paramType,
                paramName      : $paramName,
                className      : $className,
                allowUnionTypes: $allowUnionTypes,
            );
        }

        // Named type (Class)
        if ($paramType instanceof ReflectionNamedType && $class === null && $typeName === null) {
            $typeName   = $paramType->getName();
            $class      = $paramType->isBuiltin() ? null : $typeName;
            $allowsNull = $paramType->allowsNull();
        }

        // Attr: UseMethod
        $useMethodAttr = $param->getAttributes(UseMethod::class);
        $useMethod     = ! empty($useMethodAttr) ? $useMethodAttr[0]->newInstance()->method : null;

        if (! empty($attrs)) {
            /** @var WithParams $attr */
            $attr       = $attrs[0]->newInstance();
            $makeParams = $attr->params;
        }

        $classIsNull       = $class === null;
        $isEnum            = ! $classIsNull && is_a($class, class: \BackedEnum::class, allow_string: true);
        $isVo              = ! $classIsNull && is_a($class, class: AbstractValueObject::class, allow_string: true);
        $isArray           = ! $classIsNull && is_a($class, class: ArrayConvertible::class, allow_string: true);
        $isArrayResolvable = ! $classIsNull && is_a($class, class: ArrayResolvable::class, allow_string: true);

        $makeMethod = match (true) {
            $classIsNull                   => null,
            $useMethod !== null            => $useMethod,
            $isId                          => 'resolve',
            ($isVo && $resolve)            => 'parse',
            $isEnum || $isVo               => 'from',
            $isArrayResolvable && $resolve => 'resolveFromArray',
            $isArray                       => 'fromArray',
            default                        => throw KalionReflectionException::unexpectedTypeInConstructor($className, $paramName),
        };

        $propsMethod = $isArray ? 'toArray' : null;

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
            'paramName'   => $paramName,
            'typeName'    => $typeName,
            'class'       => $class,
            'isId'        => $isId,
            'allowsNull'  => $allowsNull,
            'useMethod'   => $useMethod,
            'makeParams'  => $makeParams,
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
        $cacheKey  = $className . ($resolve ? ':resolve' : '');

        if (! isset(self::$reflectionCache[$cacheKey])) {
            $reflection  = new ReflectionClass($className); // REFLECTION - cached
            $constructor = $reflection->getConstructor();

            if (! $constructor) {
                throw KalionReflectionException::constructorMissing($className);
            }

            $newParamsMake  = [];
            $newParamsProps = [];

            foreach ($constructor->getParameters() as $param) {
                $newParamsMake[] = self::getParamMetadata($param, allowUnionTypes: false, resolve: $resolve);
            }

            if (self::config()->props_from_public) {
                foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
                    $newParamsProps[] = self::getParamMetadata($prop, allowUnionTypes: true, resolve: $resolve);
                }
            } else {
                $newParamsProps = $newParamsMake;
            }

            self::$reflectionCache[$cacheKey] = [
                'make'  => $newParamsMake,
                'props' => $newParamsProps,
            ];
        }

        return self::$reflectionCache[$cacheKey];
    }

    private static function resolveUnionType(ReflectionUnionType $paramType, string $paramName, string $className, bool $allowUnionTypes): array
    {
        $allowsNull = $paramType->allowsNull();

        if (self::config()->allow_id_union) {
            $types = array_values(array_filter(
                array   : $paramType->getTypes(),
                callback: fn($t) => $t instanceof ReflectionNamedType && ! $t->isBuiltin()
            ));

            foreach ($types as $namedType) {
                $candidate = $namedType->getName();
                if (is_class_id($candidate) && ! str_ends_with($candidate, 'NullVo')) {
                    return [$candidate, $candidate, $allowsNull, true];
                }
            }
        }

        if (! $allowUnionTypes) {
            throw KalionReflectionException::unionTypeNotSupported($paramName, $className, '__construct');
        }

        $first = $paramType->getTypes()[0];
        if (! $first instanceof ReflectionNamedType) {
            throw KalionReflectionException::unionTypeNotSupported($paramName, $className, '__construct');
        }

        $typeName = $first->getName();
        $class    = $first->isBuiltin() ? null : $typeName;
        return [$typeName, $class, $allowsNull, false];
    }

    private static function reflectionDisabledData(): array
    {
        $className = static::class;

        if (isset(self::$reflectionDisabledCache[$className])) {
            return self::$reflectionDisabledCache[$className];
        }

        if (!self::config()->allow_disable_reflection) {
            return self::$reflectionDisabledCache[$className] = [
                'isDisabled'           => false,
                'useJsonSerialization' => false,
            ];
        }

        $reflection = new ReflectionClass($className); // REFLECTION - cached
        $attributes = $reflection->getAttributes(DisableReflection::class);
        $instance   = ! empty($attributes) ? $attributes[0]->newInstance() : null;

        return self::$reflectionDisabledCache[$className] = [
            'isDisabled'           => $instance !== null,
            'useJsonSerialization' => (bool)($instance?->useJsonSerialization ?? false),
        ];
    }

    protected static function make(array $data, bool $resolve = false): static
    {
        $disabled = self::reflectionDisabledData();
        if ($disabled['isDisabled']) {
            return new static(...array_values($data));
        }

        $args = [];

        $params = self::resolveConstructorParams($resolve)['make'];
        $isAssoc = arr_is_assoc($data);

        foreach ($params as $key => $meta) {
            $paramName  = $isAssoc ? $meta['paramName'] : $key;
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
                    th           : $th,
                    expectedClass: AbstractValueObject::class,
                    exception    : KalionReflectionException::failedToHydrateValueObject(static::class, $paramName, $class, $value, $th->getMessage())
                );
            }

            $args[] = $value;
        }

        try {
            return new static(...$args);
        } catch (\Throwable $th) {
            throw KalionReflectionException::resolveFailedToHydrate(
                th           : $th,
                expectedClass: self::config()->expected_exception_class,
                exception    : KalionReflectionException::failedToHydrateClass(static::class, $th->getMessage())
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

            throw KalionReflectionException::disabledReflection(static::class);
        }

        $props  = [];
        $params = self::resolveConstructorParams(false)['props'];
        foreach ($params as $meta) {
            $name   = $meta['paramName'];
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
}
