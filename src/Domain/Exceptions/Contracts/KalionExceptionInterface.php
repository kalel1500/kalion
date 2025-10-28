<?php

namespace Thehouseofel\Kalion\Domain\Exceptions\Contracts;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\ExceptionContextDto;

interface KalionExceptionInterface extends \Throwable
{
    public function getStatusCode(): int;

    public function getContext(): ?ExceptionContextDto;

    public function getResponse($data): ?array;
}
