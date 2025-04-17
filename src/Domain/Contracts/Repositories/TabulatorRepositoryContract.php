<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

interface TabulatorRepositoryContract
{
    public static function tabulatorFiltering(Builder $query, ?array $filters, array $dontFilter = []): Builder;
    public static function basicFiltering(Builder|QueryBuilder $query, ?string $field, ?string $type, mixed $value, ?string $fromOtherDBTable = null): Builder|QueryBuilder;
}
