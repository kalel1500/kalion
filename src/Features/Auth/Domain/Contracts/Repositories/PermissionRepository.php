<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Repositories;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\Collections\PermissionCollection;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\PermissionEntity;

interface PermissionRepository
{
    public function all(): PermissionCollection;

    public function searchStatic(): PermissionCollection;

    public function findByName(StringVo $permission): PermissionEntity;
}
