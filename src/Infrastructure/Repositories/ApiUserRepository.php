<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Repositories;

use Thehouseofel\Kalion\Domain\Objects\Entities\UserEntity;
use Thehouseofel\Kalion\Infrastructure\Services\Kalion;

class ApiUserRepository
{
    private string $guard = 'api';
    private string $model;

    public function __construct()
    {
        $this->model = Kalion::getClassUserModel($this->guard);
    }

    public function find(int $id): UserEntity
    {
        $data = $this->model::query()
            ->with('roles')
            ->findOrFail($id);
        return Kalion::getClassUserEntity($this->guard)::fromArray($data->toArray(), ['roles']);
    }
}
