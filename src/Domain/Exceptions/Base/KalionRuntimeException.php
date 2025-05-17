<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions\Base;

use RuntimeException;
use Thehouseofel\Kalion\Domain\Contracts\KalionException;
use Thehouseofel\Kalion\Domain\Traits\IsKalionException;
use Throwable;

abstract class KalionRuntimeException extends RuntimeException implements KalionException
{
    use IsKalionException;

    public function __construct(
        ?string    $message = null,
        ?Throwable $previous = null,
        int        $code = 0,
        ?array     $data = null,
        bool       $success = false
    )
    {
        $this->initKalionException(
            static::STATUS_CODE,
            $message ?? static::MESSAGE,
            $previous,
            $code,
            $data,
            $success
        );
    }
}