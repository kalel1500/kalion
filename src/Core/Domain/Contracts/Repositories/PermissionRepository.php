<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Contracts\Repositories;

use Thehouseofel\Kalion\Core\Domain\Objects\Entities\PermissionEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;

interface PermissionRepository
{
    public function findByName(StringVo $permission): PermissionEntity;
}
