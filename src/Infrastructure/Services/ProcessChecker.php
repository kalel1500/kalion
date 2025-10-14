<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services;

use Symfony\Component\Process\Process;
use Thehouseofel\Kalion\Domain\Exceptions\ProcessException;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\CheckableProcessVo;

final class ProcessChecker
{
    private function getWindowsCommand(CheckableProcessVo $service): string
    {
        // Windows: PowerShell + CIM. Filtramos por 'php.exe', 'artisan' y el servicio (regex escapado).
        // Devolvemos solo PIDs; si hay salida => está corriendo.
        $command = [
            'powershell',
            '-NoProfile',
            '-NonInteractive',
            '-Command',
            '"Get-CimInstance Win32_Process -Filter \\"Name=\'php.exe\'\\" |',
            'Where-Object { $_.CommandLine -match \'(?i)artisan\' -and $_.CommandLine -match ([regex]::Escape(\''.$service->translate().'\')) } |',
            'Select-Object -ExpandProperty ProcessId | Out-String"',
        ];

        // Unimos el script en una sola cadena para PowerShell
        return implode(' ', $command);
    }

    private function getLinuxCommand(CheckableProcessVo $service): string
    {
        // Linux/macOS: ps -> grep php -> grep artisan -> grep servicio (literal, -F) -> solo PID
        // Nota: el encadenado evita que 'grep' se detecte a sí mismo.
        $bash = 'ps -eo pid,args --no-headers | grep -i -- "php" | grep -i -- "artisan" | grep -F -i -- "'.$service->translate().'" | awk \'{print $1}\'';

        // Ejecutamos en bash -lc para respetar tuberías y variables
        return 'bash -lc ' . escapeshellarg($bash);
    }

    /**
     * @throws ProcessException
     */
    private function checkSystemFor(CheckableProcessVo $processName): bool
    {
        try {
            $command = so_is_windows()
                ? $this->getWindowsCommand($processName)
                : $this->getLinuxCommand($processName);

            $process = Process::fromShellCommandline($command);

            // Opcional: un timeout pequeño para no colgarnos
            $process->setTimeout(30);
            $process->run();

            if (! $process->isSuccessful()) {
                throw ProcessException::commandError($process);
            }

            // Coherencia: en ambos SO decidimos por "¿hay salida?"
            // - En Linux, si no hay matches, el último grep devuelve código 1; da igual: salida vacía => false.
            // - En Windows, PowerShell devuelve 0 aunque no haya matches; salida vacía => false.
            $output = trim($process->getOutput());

            return $output !== '';
        } catch (\Throwable $th) {
            throw new ProcessException(message: $th->getMessage(), previous: $th);
        }
    }

    /**
     * @throws ProcessException
     */
    public function isRunning(CheckableProcessVo $processName): bool
    {
        return $this->checkSystemFor($processName);
    }

    /**
     * @throws ProcessException
     */
    public function assert(CheckableProcessVo $processName, string $errorMessage = null): void
    {
        if(! $this->isRunning($processName)) {
            throw ProcessException::isNotRunningWithOptionalMessage($processName->value, $errorMessage);
        }
    }

    /**
     * @throws ProcessException
     */
    public function checkQueue(): bool
    {
        return $this->isRunning(CheckableProcessVo::queue);
    }

    /**
     * @throws ProcessException
     */
    public function assertQueue($errorMessage = null): void
    {
        $this->assert(CheckableProcessVo::queue, $errorMessage);
    }

    /**
     * @throws ProcessException
     */
    public function checkReverb(): bool
    {
        return $this->isRunning(CheckableProcessVo::reverb);
    }

    /**
     * @throws ProcessException
     */
    public function assertReverb($errorMessage = null): void
    {
        $this->assert(CheckableProcessVo::reverb, $errorMessage);
    }
}
