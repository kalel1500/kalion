<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Infrastructure\Repositories\Eloquent;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Repositories\RoleRepository;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\Collections\RoleCollection;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\RoleEntity;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Models\Role;

class EloquentRoleRepository implements RoleRepository
{
    protected string $model = Role::class;

    public function all(): RoleCollection
    {
        $data = $this->model::all();
        return RoleCollection::fromArray($data->toArray());
    }

    public function searchStatic(): RoleCollection
    {
        $data = $this->model::query()->where('is_query', false)->get();
        return RoleCollection::fromArray($data->toArray());
    }

    public function findByName(StringVo $name): RoleEntity
    {
        $data = $this->model::query()
            ->where('name', $name->value)
            ->firstOrFail();
        return RoleEntity::fromArray($data->toArray());
    }
}
