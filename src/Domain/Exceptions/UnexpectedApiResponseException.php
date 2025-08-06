<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions;

use Thehouseofel\Kalion\Domain\Exceptions\Base\KalionException;

final class UnexpectedApiResponseException extends KalionException
{
    const STATUS_CODE = 502; // Response::HTTP_BAD_GATEWAY
}
