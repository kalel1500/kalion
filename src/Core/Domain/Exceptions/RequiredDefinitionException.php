<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Exceptions;

use Thehouseofel\Kalion\Core\Domain\Exceptions\Base\KalionLogicException;

class RequiredDefinitionException extends KalionLogicException
{
    const STATUS_CODE = 500; // HTTP_INTERNAL_SERVER_ERROR
}
