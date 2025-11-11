<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Exceptions;

use Thehouseofel\Kalion\Core\Domain\Exceptions\Base\KalionRuntimeException;

class TestException extends KalionRuntimeException
{
    const STATUS_CODE = 500; // HTTP_INTERNAL_SERVER_ERROR

    public static function emptyCollection(string $name): static
    {
        $name = ucfirst($name);
        return new static("La colección de $name esta vacía.");
    }
}
