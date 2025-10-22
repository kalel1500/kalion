<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Repositories;

use Thehouseofel\Kalion\Domain\Objects\Entities\PermissionEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\StringVo;

interface PermissionRepository
{
    public function findByName(StringVo $permission): PermissionEntity;
}
