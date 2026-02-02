<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Config\KalionConfig;

final class ConfigCheck extends Command
{
    protected $signature = 'kalion:config-check';

    protected $description = 'Show the current Kalion configuration and class overrides.';

    public function handle()
    {
        $registry = KalionConfig::getRegistry();
        $orderedIdentifiers = KalionConfig::getOrderedIdentifiers();

        // 1. Mostrar Orden de Prioridad
        $this->info('1. Orden de prioridad:');
        $this->line('   ' . implode(', ', $orderedIdentifiers));
        $this->newLine();

        // 2. Mostrar Clases por Identificador
        $this->info('2. Clases registradas por identificador:');

        foreach ($registry as $id => $classes) {
            $this->warn(" [$id]");

            $rows = [];
            foreach ($classes as $key => $class) {
                $rows[] = [$key, $class];
            }

            $this->table(
                ['Config Key', 'Full Class Namespace'],
                $rows
            );
            $this->newLine();
        }

        $this->info('Nota: Si una clave no aparece aquí, se está usando el default de Kalion.');
    }
}
