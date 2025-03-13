<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Repositories;

use Thehouseofel\Kalion\Domain\Contracts\Repositories\UserRepositoryContract;
use Thehouseofel\Kalion\Domain\Objects\Entities\UserEntity;

class UserRepository implements UserRepositoryContract
{
    private string $model;

    public function __construct()
    {
        $this->model = get_class_user_model();
    }

    public function find(int $id): UserEntity
    {
        $data = $this->model::query()
            ->with('roles')
            ->findOrFail($id);
        return get_class_user_entity()::fromArray($data->toArray(), ['roles']);
    }
}
