<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Filters\Drivers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Filters\Contracts\TabulatorFilterInterface;

/**
 * @implements TabulatorFilterInterface<Builder|QueryBuilder>
 */
class EloquentTabulatorFilter implements TabulatorFilterInterface
{
    /**
     * Valores que se tratan como NULL en los filtros.
     */
    private const NULL_EQUIVALENT_VALUES = ['No aplicable', 'n/a'];

    /**
     * Aplica filtros y sorters de Tabulator sobre una Eloquent Builder query.
     */
    public function filter(mixed $query, ?array $filters, ?array $sorters = null): Builder|QueryBuilder
    {
        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $query = $this->applyFilter($query, $filter);
            }
        }

        if (!empty($sorters)) {
            foreach ($sorters as $sorter) {
                $query = $this->applySorter($query, $sorter);
            }
        }

        return $query;
    }

    // -------------------------------------------------------------------------
    // Filtering
    // -------------------------------------------------------------------------

    private function applyFilter(Builder|QueryBuilder $query, array $filter): Builder|QueryBuilder
    {
        $field = $filter['field'] ?? null;
        $type  = $filter['type'] ?? null;
        $value = $filter['value'] ?? null;

        if (empty($field)) {
            return $query;
        }

        // Sin relación: filtro directo
        if (!str_contains($field, '.')) {
            return $this->applyBasicFilter($query, $field, $type, $value);
        }

        // Con relación: separamos relación y columna
        // "user.address.city" → relación "user.address", columna "city"
        $parts   = explode('.', $field);
        $column  = array_pop($parts);
        $relPath = implode('.', array_map(
            fn(string $part) => lcfirst(str_replace('_', '', ucwords($part, '_'))),
            $parts
        ));

        // Si el valor es null, filtramos los registros que no tengan la relación
        if (is_null($value)) {
            return $query->whereDoesntHave($relPath);
        }

        return $query->whereHas($relPath, function (Builder $q) use ($column, $type, $value) {
            return $this->applyBasicFilter($q, $column, $type, $value);
        });
    }

    /**
     * Aplica un filtro simple (sin relaciones) sobre la query.
     */
    public function applyBasicFilter(
        Builder|QueryBuilder $query,
        ?string              $field,
        ?string              $type,
        mixed                $value,
    ): Builder|QueryBuilder {
        // Normalización del valor
        $value = (is_array($value) && $type === '=') ? $value[0] : $value;
        $value = in_array($value, self::NULL_EQUIVALENT_VALUES, true) ? null : $value;
        $value = match(true) {
            $value === 'true'  => true,
            $value === 'false' => false,
            default            => $value,
        };

        return match($type) {
            'like'   => $query->where($field, 'like', '%' . $value . '%'),
            'in'     => $query->whereIn($field, (array) $value),
            'not in' => $query->whereNotIn($field, (array) $value),
            '='      => is_null($value)
                ? $query->whereNull($field)
                : $query->where($field, '=', $value),
            '!='     => is_null($value)
                ? $query->whereNotNull($field)
                : $query->where($field, '!=', $value),
            'null'   => is_bool($value) && $value
                ? $query->whereNull($field)
                : $query->whereNotNull($field),
            default  => $this->tryDateFilter($query, $field, $value),
        };
    }

    private function tryDateFilter(Builder|QueryBuilder $query, string $field, mixed $value): Builder|QueryBuilder
    {
        if (is_string($value) && strtotime($value) !== false) {
            return $query->whereDate($field, $value);
        }

        return $query;
    }

    // -------------------------------------------------------------------------
    // Sorting
    // -------------------------------------------------------------------------

    private function applySorter(Builder|QueryBuilder $query, array $sorter): Builder|QueryBuilder
    {
        $field = $sorter['field'] ?? null;
        $dir   = $sorter['dir'] ?? 'asc';

        if (empty($field)) {
            return $query;
        }

        // Sin relación: orderBy directo
        if (!str_contains($field, '.')) {
            return $query->orderBy($field, $dir);
        }

        // Con relación: necesitamos un JOIN para poder ordenar
        // "relation.column" → sólo soportamos un nivel de relación para sorting
        // Para relaciones anidadas se necesitaría un subquery, que es más costoso.
        $parts    = explode('.', $field);
        $column   = array_pop($parts);
        $relName  = lcfirst(str_replace('_', '', ucwords(implode('_', $parts), '_')));

        // Obtenemos el modelo relacionado para saber su tabla real
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $parentModel    = $query->getModel();
        $relationObject = $parentModel->{$relName}();
        $relatedModel   = $relationObject->getRelated();
        $relatedTable   = $relatedModel->getTable();
        $parentTable    = $parentModel->getTable();

        // Clave foránea (por convención: relName_id en la tabla padre)
        $foreignKey = $relationObject->getForeignKeyName();

        $query->leftJoin(
            $relatedTable,
            "{$relatedTable}.id",
            '=',
            "{$parentTable}.{$foreignKey}"
        );

        // Seleccionamos la tabla padre para evitar ambigüedad en columnas
        $query->select("{$parentTable}.*");

        return $query->orderBy("{$relatedTable}.{$column}", $dir);
    }
}
