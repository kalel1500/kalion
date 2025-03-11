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
        $vendorPath = base_path() . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR;

        // Obtener la ruta del propio paquete "kalion"
        $kalionPath = $vendorPath . 'kalel1500\kalion';

        // Obtener las rutas de todos los paquetes definidos en la configuración
        if (!is_null($packages = config('kalion.packages_to_scan_for_jobs'))) {
            $packages = is_array($packages) ? $packages : explode(';', $packages);
            $packages = array_map(fn($item) => $vendorPath . $item, $packages);
        }
        if (is_null($packages)) $packages = [];

        // Escanear todas las rutas (paquete, configuracion, aplicación) para ver si existe el Job y ejecutarlo
        $this->scanPathAndRunJobIfExists([
            $kalionPath,
            ...$packages,
            src_path(),
            app_path()
        ]);
    }

    private function scanPathAndRunJobIfExists($searchPath)
    {
        $this->info('Escaneando Jobs...');

        // Obtener parámetros
        $jobName = $this->argument('job');
        $options = [
            $this->option('param1'),
            $this->option('param2'),
            $this->option('param3'),
        ];

        // Escanear todas las carpetas "Job" dentro de las rutas recibidas
        $paths = is_array($searchPath) ? $searchPath : [$searchPath];
        $paths = array_merge(...array_map([$this, 'findJobDirsOnPath'], $paths));

        // Buscar todos los Jobs que coincidan con el Job recibido [$this->argument('job')] dentro de las carpetas "escaneadas"
        $jobs = [];
        foreach ($paths as $path) {
            $dirs = scandir($path);
            foreach ($dirs as $dir) {
                // Saltar los primeros elementos que devuelve la función "scandir()"
                if (in_array($dir, [".",".."])) continue;

                // Comprobar que el item actual no sea un archivo
                $fullPathDir = $path . DIRECTORY_SEPARATOR . $dir;
                if (is_dir($fullPathDir)) continue;

                // Obtener la ruta relativa
                $relativePathDir = str_replace(base_path(), '', $fullPathDir);

                // Guardar el Job si no esta ya guardado y coincide con el job Recibido [$this->argument('job')]
                if (!in_array($relativePathDir, $jobs) && str_contains($dir, $jobName)) {
                    $jobs[] = $relativePathDir;
                }
            }
        }

        $job = (count($jobs) > 1)
            ? $this->choice('Se han encontrado varios Jobs con el mismo nombre. ¿Cual quieres ejecutar?', $jobs)
            : $jobs[0];

        // Rehacer la ruta absoluta
        $job = base_path() . $job;

        // Obtener la clase del Job (namespace + classname)
        $class = get_class_from_file($job);

        // Comprobar que la clase no sea null y exista
        if (is_null($class) || !class_exists($class)) {
            return;
        }

        // Ejecutar job
        $this->info("Ejecutando Job $jobName");
        dispatch_sync(new $class(...$options));
        $this->info("Job $jobName ejecutado");
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
            $fullPathCurrent = $path . DIRECTORY_SEPARATOR . $item;
            if (is_file($fullPathCurrent)) continue;

            // Comprobar si la carpeta actual ya es la de Jobs. En ese caso guardamos la ruta en el array "$pathsWithJobs"
            if ($item === 'Jobs') {
                $pathsWithJobs[] = normalize_path($fullPathCurrent);
                continue;
            }

            // Comprobar si existe la carpeta "Jobs" en la ruta actual. En ese caso guardamos la ruta en el array "$pathsWithJobs"
            $fullPathJobs = $fullPathCurrent .DIRECTORY_SEPARATOR . 'Jobs';
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
