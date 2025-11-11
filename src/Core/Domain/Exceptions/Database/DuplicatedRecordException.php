<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions\Database;

use Thehouseofel\Kalion\Domain\Exceptions\Base\KalionRuntimeException;

class DuplicatedRecordException extends KalionRuntimeException
{
    const STATUS_CODE = 409; // HTTP_CONFLICT;
}
