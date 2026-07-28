<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Core\Domain\Exceptions\AbortException;
use Thehouseofel\Kalion\Core\Domain\Exceptions\Contracts\KalionExceptionInterface;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\CollectionAny;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractId;

if (! function_exists('abort_d')) {
    function abort_d(
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

if (! function_exists('abort_d_if')) {
    function abort_d_if(
        bool       $condition,
        int        $code,
        string     $message,
        ?array     $data = null,
        bool       $success = false,
        ?Throwable $previous = null
    ): void
    {
        if ($condition) {
            abort_d($code, $message, $data, $success, $previous);
        }
    }
}

if (! function_exists('is_valid_bool')) {
    function is_valid_bool($value): bool
    {
        return (is_bool($value) || (($value == 0 || $value == 1)));
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

if (! function_exists('legacy_deep_clone')) {
    function legacy_deep_clone($object)
    {
        return unserialize(serialize($object));
    }
}

if (! function_exists('so_is_windows')) {
    function so_is_windows(): bool
    {
        $so = strtoupper(substr(PHP_OS, 0, 3));
        return $so === 'WIN';
    }
}

if (! function_exists('normalize_path')) {
    function normalize_path(string $path): string
    {
        return DIRECTORY_SEPARATOR === '\\'
            ? str_replace('/', '\\', $path)  // Windows
            : str_replace('\\', '/', $path); // Linux/macOS
    }
}

if (! function_exists('get_class_from_file')) {
    function get_class_from_file($filePath): ?string
    {
        $filePath = normalize_path($filePath);

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

if (! function_exists('weighted_random_numbers')) {
    function weighted_random_numbers(
        int   $quantity,
        int   $min_value,
        int   $max_value,
        array $custom_weights
    ): array
    {
        // Paso 1: Validación básica
        if ($min_value > $max_value) {
            throw new InvalidArgumentException(__('k::error.min_value_cant_be_greater_than_max'));
        }

        if ($quantity <= 0) {
            throw new InvalidArgumentException(__('k::error.amount_must_be_greater_than_number', ['number' => '0']));
        }

        // Paso 2: Lista completa de valores posibles
        $range = range($min_value, $max_value);

        // Paso 3: Calcular probabilidad restante
        $totalCustomProbability = array_sum($custom_weights);
        if ($totalCustomProbability > 100) {
            throw new InvalidArgumentException(__('k::error.sum_of_probabilities_cant_be_greater_than_100'));
        }

        $remainingProbability = 100 - $totalCustomProbability;

        // Paso 4: Números sin probabilidad definida
        $remainingNumbers  = array_diff($range, array_keys($custom_weights));
        $quantityRemaining = count($remainingNumbers);

        // Si hay números restantes, repartir el porcentaje sobrante de manera uniforme
        $fullProbabilities = $custom_weights;
        if ($quantityRemaining > 0 && $remainingProbability > 0) {
            $probByNumber = $remainingProbability / $quantityRemaining;
            foreach ($remainingNumbers as $number) {
                $fullProbabilities[$number] = $probByNumber;
            }
        } elseif ($quantityRemaining > 0 && $remainingProbability === 0) {
            // Si no hay porcentaje restante pero hay números sin probabilidad
            foreach ($remainingNumbers as $number) {
                $fullProbabilities[$number] = 0;
            }
        }

        // Paso 5: Crear la distribución ponderada
        $distribution = [];
        foreach ($fullProbabilities as $number => $probability) {
            $veces = (int)round($probability * 10); // *10 para mayor precisión
            for ($i = 0; $i < $veces; $i++) {
                $distribution[] = $number;
            }
        }

        if (empty($distribution)) {
            throw new RuntimeException(__('k::error.generated_distribution_empty'));
        }

        // Paso 6: Generar los números aleatorios según la distribución
        $resultados = [];
        for ($i = 0; $i < $quantity; $i++) {
            $indice       = array_rand($distribution);
            $resultados[] = $distribution[$indice];
        }

        return $resultados;
    }
}

if (! function_exists('random_bool_by_rate')) {
    function random_bool_by_rate(int $rate): bool
    {
        // Asegurar que el rate esté entre 0 y 100
        $rate = max(0, min(100, $rate));

        // Generar número aleatorio entre 1 y 100
        return random_int(1, 100) <= $rate;
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
