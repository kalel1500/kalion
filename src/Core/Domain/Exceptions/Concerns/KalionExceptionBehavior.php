<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Exceptions\Concerns;

use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\ExceptionContextDto;
use Thehouseofel\Kalion\Core\Domain\Support\Internal\DebugData;
use Throwable;
use UnexpectedValueException;

trait KalionExceptionBehavior
{
    const STATUS_CODE = 500;
    const MESSAGE     = '';

    protected int                  $statusCode;
    protected ?ExceptionContextDto $exceptionContext = null;

    protected function initKalionException(
        int        $statusCode,
        string     $message,
        ?Throwable $previous = null,
        int        $code = 0,
        ?array     $data = null,
        bool       $success = false,
        array      $debugData = [],
    ): void
    {
        if ($message === '') {
            throw new UnexpectedValueException(__('k::error.exception_message_can_not_be_empty', ['exception' => static::class]));
        }

        // Llamar al constructor
        parent::__construct($message, $code, $previous);

        // Guardar el statusCode
        $this->statusCode = $statusCode;

        // Guardar código y montar estructura del Json a devolver // INFO kalel1500 - mi_estructura_de_respuesta
        $this->exceptionContext = ExceptionContextDto::from(
            e        : $this,
            data     : $data,
            success  : $success,
            debugData: DebugData::normalize($debugData),
        );
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getExceptionContext(): ?ExceptionContextDto
    {
        return $this->exceptionContext;
    }

    /**
     * Laravel calls this method when building the exception log context.
     */
    public function context(): array
    {
        $context   = [];
        $debugData = $this->exceptionContext?->debugData ?? [];

        if (debug_enabled() && !empty($debugData)) {
            $context['kalion_debug'] = $debugData;
        }

        return $context;
    }
}
