<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions;

use Thehouseofel\Kalion\Domain\Exceptions\Base\KalionRuntimeException;

class ReflectionException extends KalionRuntimeException
{
    const STATUS_CODE = 500; // HTTP_INTERNAL_SERVER_ERROR

    public static function constructorMissing(string $class): static
    {
        return new static("The class $class has no constructor.");
    }

    public static function intersectionTypeNotSupported(string $param, string $class, string $method): static
    {
        return new static("Intersection types are not supported for the $$param parameter in $class::$method().");
    }

    public static function unionTypeNotSupported(string $param, string $class, string $method): static
    {
        return new static("Union types are not supported for the $$param parameter in $class::$method().");
    }

    public static function typeRequiredOnParam(string $param, string $class, string $method): static
    {
        return new static("The \$$param parameter in $class::$method() has no declared type");
    }

    public static function wrongComputedReturnType(): static
    {
        return new static("Computed method return types must not be null. Union and intersection return types are not supported.");
    }
}
