<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Console\Commands;

use Illuminate\Console\Command;

final class JobDispatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kalion:job-dispatch
                             {job : The name of the job}
                             {--p=* : The params of the job}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch Job received';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Obtener parámetros
        $jobName    = $this->argument('job');
        $params     = $this->option('p');
        $vendorPath = base_path() . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR;
        $kalionPath = $vendorPath . 'kalel1500' . DIRECTORY_SEPARATOR . 'kalion';

        // Mensaje inicial
        $this->info('Escaneando Jobs...');

        // Obtener las rutas de todos los paquetes definidos en la configuración
        if (! is_null($packages = config('kalion.packages_to_scan_for_jobs'))) {
            $packages = is_array($packages) ? $packages : explode(';', $packages);
            $packages = array_map(fn($item) => normalize_path($vendorPath . $item), $packages);
        }
        if (is_null($packages)) $packages = [];

        // Definir las rutas donde buscar los Jobs:
        $pathsToScan_packages = [
            $kalionPath, // Escanear el propio paquete "kalion"
            ...$packages, // Escanear los paquetes configurados en el ".env"
        ];
        $pathsToScan_app      = [
            src_path(), // Escanear la carpeta "src" de la propia aplicación
            app_path(), // Escanear la carpeta "app" de la propia aplicación
        ];

        // Escanear todas las carpetas "Job" dentro las rutas definidas
        $paths = array_merge(
            array_merge(...array_map(fn($path) => $this->findJobDirsOnPath($path, true), $pathsToScan_packages)),
            array_merge(...array_map(fn($path) => $this->findJobDirsOnPath($path), $pathsToScan_app)),
        );

        // Buscar todos los Jobs que coincidan con el Job recibido [$this->argument('job')] dentro de las carpetas "escaneadas"
        $jobs = [];
        foreach ($paths as $path) {
            $dirs = scandir($path);
            foreach ($dirs as $dir) {
                // Saltar los primeros elementos que devuelve la función "scandir()"
                if (in_array($dir, [".", ".."])) continue;

                // Comprobar que el item actual no sea un archivo
                $fullPathDir = $path . DIRECTORY_SEPARATOR . $dir;
                if (is_dir($fullPathDir)) continue;

                // Obtener la ruta relativa
                $relativePathDir = str_replace(base_path(), '', $fullPathDir);

                // Guardar el Job si no está ya guardado y coincide con el job Recibido [$this->argument('job')]
                if (! in_array($relativePathDir, $jobs) && str_contains($dir, $jobName)) {
                    $jobs[] = $relativePathDir;
                }
            }
        }

        // Comprobar que se haya encontrado el Job recibido
        if (empty($jobs)) {
            $this->warn("No se ha encontrado ningún Job con el nombre $jobName");
            return;
        };

        // Permitir escoger el Job al usuario si hay mas de uno
        $job = (count($jobs) > 1)
            ? $this->choice('Se han encontrado varios Jobs con el mismo nombre. ¿Cual quieres ejecutar?', $jobs)
            : $jobs[0];

        // Rehacer la ruta absoluta
        $job = base_path() . $job;

        // Obtener la clase del Job (namespace + classname)
        $class = get_class_from_file($job);

        // Comprobar que la clase no sea null y exista
        if (is_null($class)) {
            $this->warn(sprintf("No se ha encontrado la clase %s en el archivo %s", $jobName, $job));
            return;
        }

        if (! class_exists($class)) {
            $this->warn(sprintf("No se ha encontrado la clase %s", $class));
            return;
        }

        // Ejecutar job
        $this->info("Ejecutando Job $jobName");
        dispatch_sync(app()->makeWith($class, ['params' => $params]));
        $this->info("Job $jobName ejecutado");
    }

    private function findJobDirsOnPath(?string $path, bool $isPackage = false): array
    {
        if (is_null($path)) return [];

        $pathsWithJobs = [];

        // Obtener y recorrer todos los archivos que hay en la ruta recibida
        $dirs = scandir($path);

        // Si es un paquete y se encuentra la carpeta "src" escanearla directamente en vez de buscar en todas las carpetas del paquete
        if ($isPackage && in_array('src', $dirs)) {
            return $this->findJobDirsOnPath($path . DIRECTORY_SEPARATOR . 'src', $isPackage);
        }

        foreach ($dirs as $item) {

            // Saltar los primeros elementos que devuelve la función "scandir()"
            if (in_array($item, [".", ".."])) continue;

            // Saltar las carpetas ocultas
            if (str_starts_with($item, '.')) continue;

            // Comprobar que el item actual no sea un archivo
            $fullPathCurrent = $path . DIRECTORY_SEPARATOR . $item;
            if (is_file($fullPathCurrent)) continue;

            // Comprobar si la carpeta actual ya es la de Jobs. En ese caso guardamos la ruta en el array "$pathsWithJobs"
            if ($item === 'Jobs') {
                $pathsWithJobs[] = normalize_path($fullPathCurrent);
                continue;
            }

            // Comprobar si existe la carpeta "Jobs" en la ruta actual. En ese caso guardamos la ruta en el array "$pathsWithJobs"
            $fullPathJobs = $fullPathCurrent . DIRECTORY_SEPARATOR . 'Jobs';
            if (is_dir($fullPathJobs)) {
                $pathsWithJobs[] = normalize_path($fullPathJobs);
                continue;
            }

            // Si no se ha encontrado la carpeta Jobs en esta ruta, rellamar recursivamente al metodo "findJobDirsOnPath" para buscar dentro
            $pathsWithJobs = array_merge($pathsWithJobs, $this->findJobDirsOnPath($fullPathCurrent));
        }
        return $pathsWithJobs;
    }
}
