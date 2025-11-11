<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions;

use Symfony\Component\Process\Process;
use Thehouseofel\Kalion\Domain\Exceptions\Base\KalionException;

class ProcessException extends KalionException
{
    const STATUS_CODE = 500; // HTTP_INTERNAL_SERVER_ERROR

    public static function isNotRunning(string $name): static
    {
        return new static("The process $name is not running");
    }

    public static function isNotRunningWithOptionalMessage(string $name, ?string $message = null): static
    {
        if (is_null($message)) {
            return static::isNotRunning($name);
        }

        return new static($message);
    }

    public static function commandError(Process $process): static
    {
        $message = match (true) {
            '' !== trim($process->getOutput())       => $process->getOutput(),
            '' !== trim($process->getErrorOutput())  => $process->getErrorOutput(),
            '' !== trim($process->getExitCodeText()) => $process->getExitCodeText(),
        };
        return new static("Command error: $message");
    }

    public static function processFailed(string $name, string $message, ?\Throwable $previous = null): static
    {
        return new static(message: "Ha habido algún error al comprobar el proceso $name: $message", previous: $previous);
    }
}
