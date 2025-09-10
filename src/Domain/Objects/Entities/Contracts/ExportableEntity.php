<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities\Contracts;

interface ExportableEntity
{
    static function getExportColumns(): array;
}
