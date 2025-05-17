<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions\Base;

use LogicException;
use Thehouseofel\Kalion\Domain\Contracts\KalionException;
use Thehouseofel\Kalion\Domain\Traits\KalionExceptionBehavior;
use Throwable;

abstract class KalionLogicException extends LogicException implements KalionException
{
    use KalionExceptionBehavior;

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