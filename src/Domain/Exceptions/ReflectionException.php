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
        return new static("The \$$param parameter in $class::$method uses intersection, not supported.");
    }
}
