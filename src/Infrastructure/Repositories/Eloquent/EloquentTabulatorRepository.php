<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Thehouseofel\Kalion\Domain\Contracts\Repositories\TabulatorRepository;

class EloquentTabulatorRepository implements TabulatorRepository
{
    const NULL_EQUIVALENT_VALUES = [
        'No aplicable',
        'n/a',
    ];

    public function tabulatorFiltering(Builder $query, ?array $filters, array $dontFilter = []): Builder
    {
        /*Filtradores TABULATOR*/
        if (!is_null($filters)) {
            foreach ($filters as $filter) {
                $query = $this->applyFilter($query, $filter, $dontFilter);
            }
        }

        /*Ordenación TABULATOR*/
        /*if (isset($arrParams['sorters'])) {
            $sorters = $arrParams['sorters'];
            foreach ($sorters as $sorter) {
                $query = $this->applySorter($query, $sorter);
            }
        }*/

        /*Devolvemos la query construida*/
        return $query;
    }

    private function applyFilter(Builder $query, array $filter, array $dontFilter = []): Builder
    {
        $field = $filter["field"] ?? null;
        $type = $filter["type"] ?? null;
        $value = $filter["value"] ?? null;

        //Solo filtramos los que no están excepcionados
        if(in_array($field, $dontFilter)){
            return $query;
        }

        // Si no hay puntos (relaciones), filtramos directamente
        if (!str_contains($field, ".")) {
//            $originTable = $query->getModel()->getTable();
//            $field = $originTable . '.' . $field;
            return $this->basicFiltering($query, $field, $type, $value);
        }

        // Si hay puntos en el campo, separamos por punto y recorremos para crear el nombre del whereHas
        $expField = explode('.', $field);
        $relName = '';
        $colName = '';
        $numberFound = false;
        foreach ($expField as $key => $fieldPart){

            /*Si es el último elemento, pasa a ser el nombhre de la columna*/
            if ($key === count($expField)-1) {
                $colName .= $fieldPart;
                continue;
            }

            /*Si no es el último elemento, preparamos el nombre de la relación*/
            if (!$numberFound) {
                $fieldPart = lcfirst(str_replace('_','',ucwords($fieldPart,'_')));
            }

            // Si es numérico, se trata de un elemento de un array y no lo tenemos que usar
            if ($numberFound) {
                $colName = $fieldPart.'.';
                continue;
            }

            if(is_numeric($fieldPart)) {
                $numberFound = true;
                continue;
            }

            if ($key !== 0) {
                $relName .= '.';
            }
            $relName .= $fieldPart;
        }

        // Filtramos si valor null
        if (is_null($value)) {
            return $query->whereDoesntHave($relName);
        }

        // Filtramos la relacion
        return $query->whereHas($relName, function ($q) use($relName, $colName, $type, $value, $field) {
            return $this->basicFiltering($q, $colName, $type, $value);
        });
    }

    private function applySorter(Builder $query, array $sorter): Builder
    {
        $field = $sorter["field"];
        $dir = $sorter["dir"];
        $originTable = $query->getModel()->getTable();

        if (!Str::contains($field, '.')) {
            return $query->orderBy($field, $dir);
        }

        $relName = Str::beforeLast($field, '.');
        $colName = Str::afterLast($field, '.');
        $joinName = Str::plural($relName);

        $expRelName = explode('.', $relName);
        foreach ($expRelName as $key => $relName) {
            $query->join($joinName, $joinName . '.' . 'id', '=', $originTable . '.' . $relName . '_id');
            if ($key + 1 === count($expRelName)) {
                $query->orderBy($joinName . '.' . $colName, $dir);
            }
        }

        return $query;
    }

    public function basicFiltering(
        Builder|QueryBuilder $query,
        ?string $field,
        ?string $type,
        mixed $value,
        ?string $fromOtherDBTable = null
    ): Builder|QueryBuilder
    {
        // Relacionar con una tabla de otra base de datos para g2r.sist
        if (!is_null($fromOtherDBTable)) {
            $query->from($fromOtherDBTable);
        }

        $value = (is_array($value) && $type === '=') ? $value[0] : $value;
        $value = (in_array($value, static::NULL_EQUIVALENT_VALUES)) ? 'k' : $value;
        $value = ($value === 'true') ? true : $value;
        $value = ($value === 'false') ? false : $value;

        if ($type === 'null' && is_bool($value)) {
            return ($value) ? $query->whereNull($field) : $query->whereNotNull($field);
        }

        if ($type === 'like') {  // Like
            return $query->where($field, $type, '%'.$value.'%');
        }

        if ($type === '=') {
            // Si es null/ no aplicable
            if (is_null($value)) {
                $query->whereNull($field);
            } else {
                $query->where($field, $type, $value);
            }
            return $query;
        }

        if ($type === 'in') {
            return $query->whereIn($field, $value);
        }

        if ($type === 'not in') {
            return $query->whereNotIn($field, $value);
        }

        if (strtotime($value)) {
            return $query->whereDate($field, $value);
        }

        return $query;
    }
}
