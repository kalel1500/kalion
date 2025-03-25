<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Repositories;

use Thehouseofel\Kalion\Domain\Contracts\Repositories\UserRepositoryContract;
use Thehouseofel\Kalion\Domain\Objects\Entities\UserEntity;
use Thehouseofel\Kalion\Infrastructure\Services\Kalion;

class UserRepository implements UserRepositoryContract
{
    private string $model;

    public function __construct()
    {
        $this->model = Kalion::getClassUserModel();
    }

    public function find(int $id): UserEntity
    {
        $data = $this->model::query()
            ->with('roles')
            ->findOrFail($id);
        return get_class_user_entity()::fromArray($data->toArray(), ['roles']);
    }
}
