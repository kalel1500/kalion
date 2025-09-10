<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities\Contracts;

interface Exportable
{
    static function getExportColumns(): array;
}
