<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions;

use Thehouseofel\Kalion\Domain\Exceptions\Base\BasicException;

final class InvalidValueException extends BasicException
{
    const STATUS_CODE = 400; // Response::HTTP_BAD_REQUEST - Antes: 409 - HTTP_CONFLICT;
}
