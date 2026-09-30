<?php

namespace Thehouseofel\Kalion\Core\Domain\Exceptions\Contracts;

use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\ExceptionContextDto;

interface KalionExceptionInterface extends \Throwable
{
    public function getStatusCode(): int;

    public function getExceptionContext(): ?ExceptionContextDto;

    /**
     * Additional context consumed by Laravel's exception logger.
     */
    public function context(): array;
}
