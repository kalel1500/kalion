<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Infrastructure\Repositories\Eloquent;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Repositories\PermissionRepository;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\Collections\PermissionCollection;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\PermissionEntity;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Models\Permission;

class EloquentPermissionRepository implements PermissionRepository
{
    protected string $model = Permission::class;

    public function all(): PermissionCollection
    {
        $data = $this->model::all();
        return PermissionCollection::fromArray($data->toArray());
    }

    public function searchStatic(): PermissionCollection
    {
        $data = $this->model::query()->whereHas('roles', fn($q) => $q->where('is_query', false))->get();
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
