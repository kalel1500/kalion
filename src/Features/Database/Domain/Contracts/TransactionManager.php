<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Database\Domain\Contracts;

interface TransactionManager
{
    /**
     * Ejecuta la operación dentro de una transacción. Si se lanza una excepción se hace rollback y se relanza.
     * Devuelve el valor que devuelva la operación.
     *
     * @template T
     * @param callable(): T $operation
     * @return T
     */
    public function transaction(callable $operation): mixed;
}

