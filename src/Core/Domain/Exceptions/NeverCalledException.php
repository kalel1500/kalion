<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions;

use Thehouseofel\Kalion\Domain\Exceptions\Base\KalionLogicException;
use Throwable;

class NeverCalledException extends KalionLogicException
{
    const STATUS_CODE = 500;

    public function __construct(?string $message = null, ?Throwable $previous = null)
    {
        parent::__construct('INTERNAL ERROR: ' . $message, $previous);
    }
}
