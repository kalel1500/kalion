<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions\Database;

use Thehouseofel\Kalion\Domain\Exceptions\Base\KalionRuntimeException;

class RecordNotFoundException extends KalionRuntimeException
{
    const STATUS_CODE = 404; // HTTP_NOT_FOUND
}
