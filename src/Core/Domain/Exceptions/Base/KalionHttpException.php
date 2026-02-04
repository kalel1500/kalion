<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Exceptions\Base;

use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Thehouseofel\Kalion\Core\Domain\Exceptions\Concerns\KalionExceptionBehavior;
use Thehouseofel\Kalion\Core\Domain\Exceptions\Contracts\KalionExceptionInterface;
use Throwable;

abstract class KalionHttpException extends RuntimeException implements KalionExceptionInterface, HttpExceptionInterface
{
    use KalionExceptionBehavior;

    const SHOULD_RENDER_TRACE = false;
    const SHOW_LOGOUT_FORM = false;

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
            statusCode: $statusCode ?? static::STATUS_CODE,
            message   : $message ?? static::MESSAGE,
            previous  : $previous,
            code      : $code,
            data      : $data,
            success   : $success
        );
    }

    public function getHeaders(): array
    {
        return [];
    }
}
