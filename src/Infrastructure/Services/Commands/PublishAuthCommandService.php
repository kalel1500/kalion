<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Thehouseofel\Kalion\Domain\Traits\CountMethods;
use Thehouseofel\Kalion\Infrastructure\Console\Commands\PublishAuth;

final class PublishAuthCommandService
{
    use CountMethods;

    private int $steps;
    private int $number = 0;

    public function __construct(
        private PublishAuth $command,
        private bool        $reset,
    )
    {
        $this->steps = $this->countPublicMethods();
    }

    private function isReset(): bool
    {
        return $this->reset;
    }

    /**
     * Write a string as indented output.
     */
    private function line(string $message, bool $show_number = true): void
    {
        $number = $show_number ? "<fg=yellow>$this->number/$this->steps</>" : '';
        $this->command->line("  - $number $message");
    }

    public static function configure(PublishAuth $command, bool $reset): static
    {
        return new static($command, $reset);
    }

    public function publishConfigKalionUser(): static
    {
        $this->number++;

        // Delete "config/kalion.php"
        File::delete(config_path('kalion_user.php'));

        if ($this->isReset()) return $this;

        // Publish "config/kalion.php"
        $this->command->call('vendor:publish', ['--tag' => 'kalion-config-user']);
        $this->line('Configuración del paquete publicada: "config/kalion_user.php"');

        return $this;
    }

    public function modifyFile_ConfigAuth_toUpdateModel(): static
    {
        $this->number++;

        // Ruta del archivo a modificar
        $filePath = base_path('config/auth.php');

        // Leer el contenido del archivo
        $content = File::get($filePath);

        // Reemplazar la línea específica
        if ($this->isReset()) {
            $updatedContent = preg_replace(
                '/\'model\'\s*=>\s*env\(\'AUTH_MODEL\',\s*Src\\\\Shared\\\\Infrastructure\\\\Models\\\\User::class\)/',
                "'model' => env('AUTH_MODEL', App\\\\Models\\\\User::class)",
                $content
            );
        } else {
            $updatedContent = preg_replace(
                '/\'model\'\s*=>\s*env\(\'AUTH_MODEL\',\s*App\\\\Models\\\\User::class\)/',
                "'model' => env('AUTH_MODEL', Src\\\\Shared\\\\Infrastructure\\\\Models\\\\User::class)",
                $content
            );
        }

        // Guardar el archivo con el contenido actualizado
        File::put($filePath, $updatedContent);

        $this->line('Archivo "config/auth.php" modificado para actualizar el modelo de usuario');

        return $this;
    }
}
