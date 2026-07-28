<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Core\Domain\Exceptions\AbortException;
use Thehouseofel\Kalion\Core\Domain\Exceptions\Contracts\KalionExceptionInterface;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\CollectionAny;

if (! function_exists('kabort')) {
    function kabort(
        int        $statusCode,
        string     $message,
        ?array     $data = null,
        bool       $success = false,
        ?Throwable $previous = null
    ): void
    {
        throw new AbortException($statusCode, $message, $previous, data: $data, success: $success);
    }
}

if (! function_exists('kabort_if')) {
    function kabort_if(
        bool       $condition,
        int        $code,
        string     $message,
        ?array     $data = null,
        bool       $success = false,
        ?Throwable $previous = null
    ): void
    {
        if ($condition) {
            kabort($code, $message, $data, $success, $previous);
        }
    }
}

if (! function_exists('is_kalion_exception')) {
    function is_kalion_exception(Throwable $e): bool
    {
        return ($e instanceof KalionExceptionInterface);
    }
}

if (! function_exists('collect_any')) {
    function collect_any(array $array): CollectionAny
    {
        return CollectionAny::fromArray($array);
    }
}

if (! function_exists('enum_values')) {
    /**
     * @param class-string<\BackedEnum|\UnitEnum> $class
     * @return array
     */
    function enum_values(string $class): array
    {
        return array_map(fn($case) => $case->value, $class::cases());
    }
}
