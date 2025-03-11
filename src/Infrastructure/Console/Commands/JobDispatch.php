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
    protected $signature = 'job:dispatch {job} {--param1=} {--param2=} {--param3=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch Job received';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        // Buscar el Job en este paquete para ver si existe y ejecutarlo
        $executed = $this->scanPathAndRunJobIfExists(KALION_PATH);
        if ($executed) return;

        // Escanear las carpetas de otros paquetes definidas en la configuración para ver si existe el Job y ejecutarlo
        if (!is_null($packages = config('kalion.packages_to_scan_for_jobs'))) {
            $packages = is_array($packages) ? $packages : explode(';', $packages);
            $packages = array_map(fn($item) => base_path().'/vendor/'.$item, $packages);
            $executed = $this->scanPathAndRunJobIfExists($packages);
            if ($executed) return;
        }

        // Escanear el proyecto (/scr) para ver si existe el Job y ejecutarlo
        $this->scanPathAndRunJobIfExists([src_path(), app_path()]);
    }

    private function scanPathAndRunJobIfExists($searchPath): bool
    {
        $paths = is_array($searchPath) ? $searchPath : [$searchPath];
        $paths = array_merge(...array_map([$this, 'findJobDirsOnPath'], $paths));
        foreach ($paths as $path) {
            $executed = $this->tryDispatchJobFromPath($path);
            if ($executed) return true;
        }
        return false;
    }

    private function tryDispatchJobFromPath(string $jobPath): bool
    {
        $executed = false;
        $filePath = $jobPath . DIRECTORY_SEPARATOR . $this->argument('job') . '.php';
        $class = get_class_from_file($filePath);
        if (!is_null($class) && class_exists($class)) {
            dispatch(new $class($this->option('param1'), $this->option('param2'), $this->option('param3')));
            $executed = true;
        }
        return $executed;
    }

    private function findJobDirsOnPath($path): array
    {
        $pathsWithJobs = [];

        // Obtener y recorrer todos los archivos que hay en la ruta recibida
        $dirs = scandir($path);
        foreach ($dirs as $item) {

            // Saltar los primeros elementos que devuelve la función "scandir()"
            if (in_array($item, [".",".."])) continue;

            // Comprobar que el item actual no sea un archivo
            $fullPathCurrent = $path.DIRECTORY_SEPARATOR.$item;
            if (is_file($fullPathCurrent)) continue;

            // Comprobar si la carpeta actual ya es la de Jobs
            if ($item === 'Jobs') {
                $pathsWithJobs[] = $fullPathCurrent;
                continue;
            }

            // Comprobar si existe la carpeta "Jobs" en la ruta actual
            $fullPathJobs = $fullPathCurrent.DIRECTORY_SEPARATOR.'Jobs';
            if (!is_dir($fullPathJobs)) {
                // Si no existe volver a llama al metodo "findJobDirsOnPath" recursivamente para buscar dentro
                $pathsWithJobs = array_merge($pathsWithJobs, $this->findJobDirsOnPath($fullPathCurrent));
                continue;
            }

            // En caso de que se encuentre la carpeta "Jobs" la guardamos en el array "$pathsWithJobs"
            $pathsWithJobs[] = $fullPathJobs;
        }
        return $pathsWithJobs;
    }
}
