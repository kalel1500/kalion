<?php

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Filters\Contracts;

/**
 * @template TQuery
 */
interface TabulatorFilterInterface
{
    /**
     * Aplica los filtros y sorters de Tabulator sobre una query.
     *
     * @param TQuery       $query    Objeto query (Builder de Eloquent, QueryBuilder, etc.)
     * @param  array|null  $filters  Array de filtros enviados por Tabulator
     * @param  array|null  $sorters  Array de sorters enviados por Tabulator
     * @return TQuery                La query modificada
     */
    public function filter(mixed $query, ?array $filters, ?array $sorters = null);
}
