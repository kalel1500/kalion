<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions\Base;

use Throwable;

abstract class BasicException extends KalionException
{
    const STATUS_CODE = 500;
    const MESSAGE = '';

    public function __construct(
        ?string     $message = null,
        ?Throwable $previous = null,
        int        $code = 0,
        ?array     $data = null,
        bool       $success = false,
    )
    {
        $message = $message ?? static::MESSAGE;
        parent::__construct(static::STATUS_CODE, $message, $previous, $code, $data, $success);
    }
}
