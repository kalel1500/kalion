<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Database\Infrastructure;

use Illuminate\Support\Facades\DB;
use Thehouseofel\Kalion\Features\Database\Domain\Contracts\TransactionManager;

/**
 * IMPORTANTE: solo cubre la conexión por defecto. Las escrituras en otras conexiones
 * (p.ej. "mysql_old") NO se revierten y deben hacerse fuera de la transacción.
 */
final class LaravelTransactionManager implements TransactionManager
{
    public function transaction(callable $operation): mixed
    {
        return DB::transaction($operation);
    }
}

