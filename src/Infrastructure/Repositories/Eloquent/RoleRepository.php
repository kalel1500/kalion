<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Repositories\Eloquent;

use Thehouseofel\Kalion\Domain\Contracts\Repositories\RoleRepositoryContract;
use Thehouseofel\Kalion\Domain\Objects\Entities\RoleEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Infrastructure\Models\Role;

class RoleRepository implements RoleRepositoryContract
{
    protected string $model = Role::class;

    public function findByName(ModelString $name): RoleEntity
    {
        $data = $this->model::query()
            ->where('name', $name->value())
            ->firstOrFail();
        return RoleEntity::fromArray($data->toArray());
    }
}
