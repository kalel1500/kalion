<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Exceptions;

use Thehouseofel\Kalion\Core\Domain\Exceptions\Base\KalionException;

class UnexpectedApiResponseException extends KalionException
{
    const STATUS_CODE = 502; // Response::HTTP_BAD_GATEWAY
}
