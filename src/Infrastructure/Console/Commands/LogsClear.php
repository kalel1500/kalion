<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

final class LogsClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear log files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logPath = storage_path('logs');

        // Limpiar archivos de log
        if (!File::exists($logPath)) {
            $this->warn('La carpeta de logs no existe.');
            return;
        }

        foreach (File::files($logPath) as $file) {
            File::put($file->getPathname(), ''); // Vaciar el contenido del archivo
        }

        $this->info('Logs have been cleared');

        // Ajustar permisos (solo en Linux/Mac)
        if (so_is_windows()) {
            // $this->info('En Windows, no es necesario ajustar permisos.');
            return;
        }

        try {
            shell_exec("chmod -R 775 {$logPath}");
            // shell_exec("chown -R www-data:www-data {$logPath}"); // Sirve para asegurar que el usuario y el grupo de Apache tengan la propiedad del archivo (hay que ajustar según el servidor)
            $this->info('Permisos ajustados correctamente.');
        } catch (\Exception $e) {
            $this->error('No se pudieron ajustar los permisos: ' . $e->getMessage());
        }
    }
}
