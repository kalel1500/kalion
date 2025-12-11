<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Shared\Infrastructure\Repositories\Eloquent;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories\PermissionRepository;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\Collections\PermissionCollection;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\PermissionEntity;
use Thehouseofel\Kalion\Features\Shared\Infrastructure\Models\Permission;

class EloquentPermissionRepository implements PermissionRepository
{
    protected string $model = Permission::class;

    public function all(): PermissionCollection
    {
        $data = $this->model::all();
        return PermissionCollection::fromArray($data->toArray());
    }

    public function findByName(StringVo $permission): PermissionEntity
    {
        $data = $this->model::query()
            ->with('roles')
            ->where('name', $permission->value)
            ->firstOrFail();
        return PermissionEntity::fromArray($data->toArray(), ['roles']);
    }
}
