<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Repositories\Eloquent;

use Thehouseofel\Kalion\Core\Domain\Contracts\Repositories\PermissionRepository;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\PermissionEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Core\Infrastructure\Models\Permission;

class EloquentPermissionRepository implements PermissionRepository
{
    protected string $model = Permission::class;

    public function findByName(StringVo $permission): PermissionEntity
    {
        $data = $this->model::query()
            ->with('roles')
            ->where('name', $permission->value)
            ->firstOrFail();
        return PermissionEntity::fromArray($data->toArray(), ['roles']);
    }
}
