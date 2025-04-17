<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Repositories\Eloquent;

use Thehouseofel\Kalion\Domain\Contracts\Repositories\PermissionRepositoryContract;
use Thehouseofel\Kalion\Domain\Objects\Entities\PermissionEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Infrastructure\Models\Permission;

class PermissionRepository implements PermissionRepositoryContract
{
    protected string $model = Permission::class;

    public function findByName(ModelString $permission): PermissionEntity
    {
        $data =  $this->model::query()
            ->with('roles')
            ->where('name', $permission->value())
            ->firstOrFail();
        return PermissionEntity::fromArray($data->toArray(), ['roles']);
    }
}
