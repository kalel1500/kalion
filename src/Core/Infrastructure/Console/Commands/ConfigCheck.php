<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\TableSeparator;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Config\KalionConfig;

final class ConfigCheck extends Command
{
    protected $signature = 'kalion:config-check';

    protected $description = 'Show the current Kalion configuration and class overrides.';

    public function handle()
    {
        $registry = KalionConfig::getRegistry();
        $orderedIdentifiers = KalionConfig::getOrderedIdentifiers();

        $this->info('Overridden Kalion Configuration Classes (the ids are displayed in order of priority):');
        $rows = [];
        $lastId = null;
        foreach ($orderedIdentifiers as $id) {
            // Si no es el primer bloque, añadimos una línea separadora real
            if ($lastId !== null) {
                $rows[] = new TableSeparator();
            }

            foreach ($registry[$id] as $key => $class) {
                $rows[] = [
                    // Solo mostramos el ID si es distinto al anterior, si no, celda vacía
                    ($id !== $lastId) ? "<fg=yellow>$id</>" : '',
                    $key,
                    $class
                ];
                $lastId = $id;
            }
        }

        if (empty($rows)) {
            $this->line('   No hay sobrescrituras registradas.');
        } else {
            $this->table(
                ['ID', 'Config Key', 'Full Class Namespace'],
                $rows
            );
        }

        $this->info('Nota: Si una clave no aparece aquí, se está usando el default de Kalion.');
    }
}
