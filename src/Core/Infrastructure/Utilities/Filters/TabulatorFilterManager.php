<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Filters;

use Illuminate\Support\Manager;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Filters\Drivers\EloquentTabulatorFilter;

/**
 * @mixin \Thehouseofel\Kalion\Core\Infrastructure\Utilities\Filters\Contracts\TabulatorFilterInterface
 */
class TabulatorFilterManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return $this->config->get('kalion.tabulator_filter.driver', 'eloquent');
    }

    protected function createEloquentDriver(): EloquentTabulatorFilter
    {
        return new EloquentTabulatorFilter();
    }

    // En el futuro:
    // protected function createDoctrineDriver(): DoctrineTabulatorFilter { ... }
    // protected function createCsvDriver(): CsvTabulatorFilter { ... }
}
