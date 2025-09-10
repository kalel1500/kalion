<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Repositories;

use Thehouseofel\Kalion\Domain\Objects\Entities\PermissionEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;

interface PermissionRepository
{
    public function findByName(ModelString $permission): PermissionEntity;
}
