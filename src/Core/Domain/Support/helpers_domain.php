<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Core\Domain\Exceptions\AbortException;
use Thehouseofel\Kalion\Core\Domain\Exceptions\Contracts\KalionExceptionInterface;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\CollectionAny;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractId;
use Thehouseofel\Kalion\Core\Domain\Support\Path;

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

if (! function_exists('legacy_json_to_array')) {
    function legacy_json_to_array($object): array|object|null
    {
        $string = json_encode($object);
        if (! $string) {
            return null;
        }
        return json_decode($string, true);
    }
}

if (! function_exists('legacy_json_to_object')) {
    function legacy_json_to_object($object): array|object|null
    {
        $string = json_encode($object);
        if (! $string) {
            return null;
        }
        return json_decode($string);
    }
}

if (! function_exists('so_is_windows')) {
    function so_is_windows(): bool
    {
        $so = strtoupper(substr(PHP_OS, 0, 3));
        return $so === 'WIN';
    }
}

if (! function_exists('get_class_from_file')) {
    function get_class_from_file($filePath): ?string
    {
        $filePath = Path::normalize($filePath);

        if (! file_exists($filePath)) return null;

        $contents = file_get_contents($filePath);

        $namespace = null;
        if (preg_match('/namespace\s+([^;]+);/', $contents, $matches)) {
            $namespace = trim($matches[1]);
        }

        if (is_null($namespace)) {
            return null;
        }

        // Buscar el nombre de la clase ignorando cualquier palabra antes de "class"
        if (preg_match('/\bclass\s+([a-zA-Z0-9_]+)/', $contents, $matches)) {
            $className = trim($matches[1]);
            return $namespace . '\\' . $className;
        }

        return null;
    }
}

if (! function_exists('is_class_id')) {
    function is_class_id(string $class): bool
    {
        if (! class_exists($class)) {
            return false;
        }

        // Obtener solo el nombre corto de la clase (sin namespace)
        $short = substr(strrchr($class, '\\') ?: $class, 1) ?: $class;

        if (str_starts_with($short, 'Id')) {
            return true;
        }

        return is_subclass_of($class, AbstractId::class);
    }
}

if (! function_exists('is_generic_object')) {
    function is_generic_object(mixed $variable): bool
    {
        return is_object($variable) && get_class($variable) === 'stdClass';
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
