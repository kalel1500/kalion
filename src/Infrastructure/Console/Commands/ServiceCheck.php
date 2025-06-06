<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\Env;

final class ServiceCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kalion:service-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the service specified';

    /**
     * Execute the console command.
     */
    public function handle()
    {
//        return false;
        if (so_is_windows()) {
            if (Env::new()->isLocal()) {
                $this->info('EL servicio de colas esta activo');
                return true;
            }
            return false;
            /*$process = new Process(['powershell.exe', 'Get-Process php | Where-Object {$_.MainWindowTitle -like "*queue:work*"}']);
            $process = new Process(['wmic', 'process', 'get', 'CommandLine', '|', 'findstr', '/C:"queue:work"']);
            $process = Process::fromShellCommandline('tasklist /FI "IMAGENAME eq php.exe" /FO CSV');
            $process = Process::fromShellCommandline('powershell.exe Get-WmiObject win32_process -Filter "CommandLine like \'%queue:work%\'"|select CreationDate,ProcessId,CommandLine');
            $process = new Process(['powershell.exe', 'Get-WmiObject win32_process -Filter "CommandLine like \'%queue:work%\'"|select CreationDate,ProcessId,CommandLine']);
            dd($process);*/
        }

        $process = Process::fromShellCommandline('ps -ef | grep queue:work');
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('Error al ejecutar el comando para verificar los trabajadores de cola');
            return false;
        }

        $output = $process->getOutput();
        if (!empty($output) && str_contains($output, 'artisan') && str_contains($output, 'queue:work')) {
            $this->info('EL servicio de colas esta activo');
        } else {
            $this->info('EL servicio de colas esta inactivo');
        }
        return true;
    }
}
