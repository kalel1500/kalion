<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Shared\Infrastructure\Repositories\Eloquent;

use Thehouseofel\Kalion\Core\Domain\Objects\Entities\UserEntity;
use Thehouseofel\Kalion\Core\Infrastructure\Services\Kalion;

class EloquentUserRepository
{
    protected string $model;

    public function __construct()
    {
        $this->model = Kalion::getClassUserModel();
    }

    public function find(int $id): UserEntity
    {
        $data = $this->model::query()
            ->with('roles')
            ->findOrFail($id);
        return Kalion::getClassUserEntity()::fromArray($data->toArray(), ['roles']);
    }
}
