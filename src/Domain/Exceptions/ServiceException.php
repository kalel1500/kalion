<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions;

use Thehouseofel\Kalion\Domain\Exceptions\Base\KalionRuntimeException;

class ServiceException extends KalionRuntimeException
{
    const STATUS_CODE = 500; // HTTP_INTERNAL_SERVER_ERROR
}
