<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions\Base;

use LogicException;
use Thehouseofel\Kalion\Domain\Exceptions\Concerns\KalionExceptionBehavior;
use Thehouseofel\Kalion\Domain\Exceptions\Contracts\KalionExceptionInterface;
use Throwable;

abstract class KalionLogicException extends LogicException implements KalionExceptionInterface
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
