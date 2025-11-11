<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Shared\Infrastructure\Repositories\Eloquent;

use Thehouseofel\Kalion\Core\Domain\Objects\Entities\UserEntity;
use Thehouseofel\Kalion\Core\Infrastructure\Services\Kalion;

class EloquentApiUserRepository
{
    protected string $guard = 'api';
    protected string $model;

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
