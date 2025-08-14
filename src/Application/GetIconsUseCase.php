<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Application;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Icons\IconDto;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Icons\ViewIconsDto;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
final class GetIconsUseCase
{
    public function __invoke(bool $showNameShort): ViewIconsDto
    {
        // Ruta a la carpeta de iconos
        $iconPath = KALION_PATH . '/resources/views/components/icon';

        if (!is_dir($iconPath)) {
            abort(404, "La carpeta de iconos no se encontró en: $iconPath");
        }

        // Obtener todos los archivos Blade de la carpeta
        $icons = collect(File::files($iconPath))
            ->filter(function ($file) {
                return $file->getExtension() === 'php'; // Aseguramos que sean PHP (Blade)
            })
            ->map(function ($file) {
                // Extraer el nombre del componente en kebab-case
                $prefix = 'kal::icon.';
                $name = Str::kebab($file->getBasename('.blade.php'));
                return IconDto::fromArray(['name' => $prefix . $name, 'name_short' => $name]);
            });

        return ViewIconsDto::fromArray([
            'icons' => $icons->toArray(),
            'show_name_short' => $showNameShort,
        ]);
    }
}
