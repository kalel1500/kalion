<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Internal;

use Thehouseofel\Kalion\Core\Domain\Support\Path;

/**
 * @internal This class is intended for internal package usage only.
 */
final class FileClass
{
    public static function resolve(string $filePath): ?string
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
