<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Thehouseofel\Kalion\Domain\Exceptions\Base\KalionException;
use Thehouseofel\Kalion\Domain\Exceptions\AbortException;
use Thehouseofel\Kalion\Domain\Objects\Collections\CollectionAny;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\SubRelationDataDo;

if (!function_exists('str_camel')) {
    function str_camel(?string $string): ?string
    {
        if (is_null($string)) return null;
        return lcfirst(Str::camel(Str::slug($string)));
    }
}

if (!function_exists('str_truncate')) {
    function str_truncate(string $string, int $length = 100, string $append = '&hellip;'): ?string
    {
        // Check append length
        if ($length <= strlen($append)) {
            return null;
        }

        // Srting min length
        if (strlen($string) <= $length) {
            return $string;
        }

        // Truncate code

        $length = $length - strlen($append);
        $string = trim($string);

        // Version 1 (este código no corta palabras por la mitad)
//        $string = wordwrap($string, $length);
//        $string = explode("\n", $string, 2);
//        $string = $string[0] . $append;

        // Version 2
        return substr($string, 0, $length) . $append;
    }
}

if (!function_exists('validate_email')) {
    function validate_email(string $email): string|false
    {
        return (filter_var($email, FILTER_VALIDATE_EMAIL));
    }
}

if (!function_exists('explode_by_uppercase')) {
    function explode_by_uppercase(string $value): array|false
    {
        return preg_split('/(?=[A-Z])/', $value, -1, PREG_SPLIT_NO_EMPTY);
    }
}

if (!function_exists('abort_d')) {
    function abort_d(
        int $statusCode,
        string $message,
        ?array $data = null,
        bool $success = false,
        ?Throwable $previous = null
    ): void
    {
        throw new AbortException($statusCode, $message, $previous, $data, $success);
    }
}

if (!function_exists('abort_d_if')) {
    function abort_d_if(
        bool $condition,
        int $code,
        string $message,
        ?array $data = null,
        bool $success = false,
        ?Throwable $previous = null
    ): void
    {
        if ($condition) {
            abort_d($code, $message, $data, $success, $previous);
        }
    }
}

if (!function_exists('is_valid_bool')) {
    function is_valid_bool($value): bool
    {
        return (is_bool($value) || (($value === 0 || $value === 1)));
    }
}

if (!function_exists('is_kalion_exception')) {
    function is_kalion_exception(Throwable $e): bool
    {
        return ($e instanceof KalionException);
    }
}

if (!function_exists('collect_any')) {
    function collect_any(array $array): CollectionAny
    {
        return CollectionAny::fromArray($array);
    }
}

if (!function_exists('object_to_array')) {
    function object_to_array($object): array|object
    {
        $string = json_encode($object);
        return json_decode($string, true);
    }
}

if (!function_exists('array_to_object')) {
    function array_to_object($object): array|object
    {
        $string = json_encode($object);
        return json_decode($string);
    }
}

if (!function_exists('obj_clone')) {
    function obj_clone($object)
    {
        return unserialize(serialize($object));
    }
}

if (!function_exists('array_keep')) {
    function array_keep(array $arrayData, array $arrayKeys): array
    {
        return array_intersect_key($arrayData, array_flip($arrayKeys));
    }
}

if (!function_exists('array_delete')) {
    function array_delete(array $arrayData, array $arrayKeys): array
    {
        return array_diff_key($arrayData, array_flip($arrayKeys));
    }
}

if (!function_exists('array_diff_assoc_deep')) {
    function array_diff_assoc_deep(array $array1, array $array2): array {
        $difference = array();
        foreach($array1 as $key => $value) {
            if( is_array($value) ) {
                if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = array_diff_assoc_deep($value, $array2[$key]);
                    if(!empty($new_diff)) {
                        $difference[$key] = $new_diff;
                    }
                }
            } else if( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
                $difference[$key] = $value;
            }

            /*if (
                (!is_array($value) && (!array_key_exists($key, $array2) || $array2[$key] !== $value))
                ||
                (is_array($value) && (!isset($array2[$key]) || !is_array($array2[$key])))
            ) {
                $difference[$key] = $value;
                continue;
            }

            if (is_array($value) && (isset($array2[$key]) && is_array($array2[$key]))) {
                $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
                if(!empty($new_diff)) {
                    $difference[$key] = $new_diff;
                }
            }*/
        }
        return $difference;
    }

}

if (!function_exists('array_unshift_assoc')) {
    function array_unshift_assoc($arr, $key, $val): array
    {
        $arr = array_reverse($arr, true);
        $arr[$key] = $val;
        return array_reverse($arr, true);
    }
}

if (!function_exists('get_info_from_relation_with_flag')) {
    /**
     * @param string $relation
     * @param bool|string|null $isFull
     * @return array{string, bool|string|null}
     */
    function get_info_from_relation_with_flag(string $relation, bool|string|null $isFull = null): array
    {
        if (str_contains($relation, ':')) {
            [$relation, $flag] = explode(':', $relation);
            $isFull = $flag === 'f' ? true : ($flag === 's' ? false : $flag);
        }
        return [$relation, $isFull];
    }
}

if (!function_exists('so_is_windows')) {
    function so_is_windows(): bool
    {
        $so = strtoupper(substr(PHP_OS, 0, 3));
        return $so === 'WIN';
    }
}

if (!function_exists('str_contains_html')) {
    function str_contains_html(string $value): bool
    {
        return $value !== strip_tags($value);
    }
}

if (!function_exists('normalize_path')) {
    function normalize_path(string $path): string
    {
        return DIRECTORY_SEPARATOR === '\\'
            ? str_replace('/', '\\', $path)  // Windows
            : str_replace('\\', '/', $path); // Linux/macOS
    }
}

if (!function_exists('pipe_str_to_array')) {
    function pipe_str_to_array(array|string $value): array
    {
        return is_array($value)
            ? $value
            : explode('|', $value);
    }
}

if (!function_exists('get_class_from_file')) {
    function get_class_from_file($filePath): ?string
    {
        $filePath = normalize_path($filePath);

        if (!file_exists($filePath)) return null;

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