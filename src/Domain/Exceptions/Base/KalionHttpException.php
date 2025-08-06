<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions\Base;

use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Thehouseofel\Kalion\Domain\Contracts\KalionExceptionInterface;
use Thehouseofel\Kalion\Domain\Traits\KalionExceptionBehavior;
use Throwable;

abstract class KalionHttpException extends RuntimeException implements KalionExceptionInterface, HttpExceptionInterface
{
    use KalionExceptionBehavior;

    public function __construct(
        ?int       $statusCode = null,
        ?string    $message = null,
        ?Throwable $previous = null,
        int        $code = 0,
        ?array     $data = null,
        bool       $success = false
    )
    {
        $this->initKalionException(
            $statusCode ?? static::STATUS_CODE,
            $message ?? static::MESSAGE,
            $previous,
            $code,
            $data,
            $success
        );
    }

    public function getHeaders(): array
    {
        return [];
    }
}
