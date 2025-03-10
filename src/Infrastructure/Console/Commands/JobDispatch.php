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
        $namespace = 'Thehouseofel'.DIRECTORY_SEPARATOR.'Kalion'.DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Jobs';
        $executed = $this->tryExecJobInNamespace($namespace);
        if ($executed) return;

        // Escanear las carpetas de otros paquetes definidas en la configuración para ver si existe el Job y ejecutarlo
        foreach (config('kalion.job_paths_from_other_packages') as $namespace) {
            $executed = $this->tryExecJobInNamespace($namespace);
            if ($executed) return;
        }

        // Escanear el proyecto (/scr) para ver si existe el Job y ejecutarlo
        $paths = $this->scanJobDirsProject(src_path());
        foreach ($paths as $path) {
            $pos = strpos($path, 'src')+1;
            $namespace  = substr_replace($path, 'S', 0, $pos);
            $executed = $this->tryExecJobInNamespace($namespace);
            if ($executed) return;
        }
    }

    private function tryExecJobInNamespace(string $namespace): bool
    {
        $executed = false;
        $class = $namespace.DIRECTORY_SEPARATOR.$this->argument('job');
        $class = str_replace('/', '\\', $class);
        if (class_exists($class)) {
            dispatch(new $class($this->option('param1'), $this->option('param2'), $this->option('param3')));
            $executed = true;
        }
        return $executed;
    }

    private function scanJobDirsProject($path): array
    {
        $pathsWithJobs = [];
        $dirs = scandir($path);
        foreach ($dirs as $item) {
            if (in_array($item, array(".",".."))) continue;
            $fullPathCurrent = $path.DIRECTORY_SEPARATOR.$item;
            if (is_file($fullPathCurrent)) continue;
            $fullPathInfra = $fullPathCurrent.DIRECTORY_SEPARATOR.'Infrastructure';
            $currentHasInfra = is_dir($fullPathInfra);
            if ($currentHasInfra) {
                $fullPathJobs = $fullPathInfra.DIRECTORY_SEPARATOR.'Jobs';
                $infraHasJobs = is_dir($fullPathJobs);
                if ($infraHasJobs) {
                    $pathsWithJobs[] = $fullPathJobs;
                }
            } else {
                $this->scanJobDirsProject($fullPathCurrent);
            }
        }
        return $pathsWithJobs;
    }
}
