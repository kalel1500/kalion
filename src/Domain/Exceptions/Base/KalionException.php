<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions\Base;

use Exception;
use Thehouseofel\Kalion\Domain\Contracts\KalionExceptionInterface;
use Thehouseofel\Kalion\Domain\Exceptions\Concerns\KalionExceptionBehavior;
use Throwable;

abstract class KalionException extends Exception implements KalionExceptionInterface
{
    use KalionExceptionBehavior;

    public function __construct(
        ?string    $message = null,
        ?Throwable $previous = null,
        int        $code = 0,
        ?array     $data = null,
        bool       $success = false,
        ?int       $statusCode = null,
    )
    {
        $this->initKalionException(
            statusCode: $statusCode ?? static::STATUS_CODE,
            message   : $message ?? static::MESSAGE,
            previous  : $previous,
            code      : $code,
            data      : $data,
            success   : $success
        );
    }
}
