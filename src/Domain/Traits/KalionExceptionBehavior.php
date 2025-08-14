<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Traits;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\ExceptionContextDto;
use Throwable;
use UnexpectedValueException;

trait KalionExceptionBehavior
{
    const STATUS_CODE = 500;
    const MESSAGE     = '';

    protected int                  $statusCode;
    protected ?ExceptionContextDto $context = null;

    protected function initKalionException(
        int        $statusCode,
        string     $message,
        ?Throwable $previous = null,
        int        $code = 0,
        ?array     $data = null,
        bool       $success = false
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
        $this->context = ExceptionContextDto::from($this, $data, $success, $this->getResponse($data));
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getContext(): ?ExceptionContextDto
    {
        return $this->context;
    }

    public function getResponse($data): ?array
    {
        return null;
    }
}
