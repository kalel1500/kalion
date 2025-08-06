<?php

namespace Thehouseofel\Kalion\Domain\Contracts;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\ExceptionContextDo;

interface KalionExceptionInterface extends \Throwable
{
    public function getStatusCode(): int;
    public function getContext(): ?ExceptionContextDo;
    public function getResponse($data): ?array;
}
