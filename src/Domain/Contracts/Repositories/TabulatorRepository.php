<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

interface TabulatorRepository
{
    public function tabulatorFiltering(Builder $query, ?array $filters, array $dontFilter = []): Builder;
    public function basicFiltering(Builder|QueryBuilder $query, ?string $field, ?string $type, mixed $value, ?string $fromOtherDBTable = null): Builder|QueryBuilder;
}
