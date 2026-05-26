<?php

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Filters\Contracts;

interface TabulatorFilterInterface
{
    /**
     * Aplica los filtros y sorters de Tabulator sobre una query.
     *
     * @param  mixed       $query    Objeto query (Builder de Eloquent, QueryBuilder, etc.)
     * @param  array|null  $filters  Array de filtros enviados por Tabulator
     * @param  array|null  $sorters  Array de sorters enviados por Tabulator
     * @return mixed                 La query modificada
     */
    public function filter(mixed $query, ?array $filters, ?array $sorters = null): mixed;
}
