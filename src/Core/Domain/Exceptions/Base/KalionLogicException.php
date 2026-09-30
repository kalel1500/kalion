<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Exceptions\Base;

use LogicException;
use Thehouseofel\Kalion\Core\Domain\Exceptions\Concerns\KalionExceptionBehavior;
use Thehouseofel\Kalion\Core\Domain\Exceptions\Contracts\KalionExceptionInterface;
use Throwable;

class KalionLogicException extends LogicException implements KalionExceptionInterface
{
    use KalionExceptionBehavior;

    /**
     * @param array|null $data Public, JSON-serializable data included in the response.
     * @param array $debugData Internal diagnostic data; it may contain non-serializable values.
     */
    public function __construct(
        ?string    $message = null,
        ?Throwable $previous = null,
        int        $code = 0,
        ?array     $data = null,
        bool       $success = false,
        ?int       $statusCode = null,
        array      $debugData = [],
    )
    {
        $this->initKalionException(
            statusCode: $statusCode ?? static::STATUS_CODE,
            message   : $message ?? static::MESSAGE,
            previous  : $previous,
            code      : $code,
            data      : $data,
            success   : $success,
            debugData : $debugData,
        );
    }
}
