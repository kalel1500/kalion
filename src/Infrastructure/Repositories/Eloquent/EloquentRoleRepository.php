<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Repositories\Eloquent;

use Thehouseofel\Kalion\Domain\Contracts\Repositories\RoleRepository;
use Thehouseofel\Kalion\Domain\Objects\Entities\RoleEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Infrastructure\Models\Role;

class EloquentRoleRepository implements RoleRepository
{
    protected string $model = Role::class;

    public function findByName(StringVo $name): RoleEntity
    {
        $data = $this->model::query()
            ->where('name', $name->value())
            ->firstOrFail();
        return RoleEntity::fromArray($data->toArray());
    }
}
