<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\Collections\PermissionCollection;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\PermissionEntity;

interface PermissionRepository
{
    public function all(): PermissionCollection;

    public function findByName(StringVo $permission): PermissionEntity;
}
