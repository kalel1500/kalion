<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Exceptions\Database;

use Thehouseofel\Kalion\Core\Domain\Exceptions\Base\KalionRuntimeException;

class RecordAlreadyExistsException extends KalionRuntimeException
{
    const STATUS_CODE = 409; // HTTP_CONFLICT
}
