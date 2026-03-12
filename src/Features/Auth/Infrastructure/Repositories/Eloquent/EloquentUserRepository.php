<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Infrastructure\Repositories\Eloquent;

class EloquentUserRepository
{
    protected string $guard = 'web';
    protected string $model;
    protected string $entity;

    public function __construct()
    {
        $this->model = kauth($this->guard)->getClassUserModel();
        $this->entity = kauth($this->guard)->getClassUserEntity();
    }

    /**
     * @return class-string<\Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AuthEntity>
     */
    public function find(int $id)
    {
        $data = $this->model::query()
            ->with('roles')
            ->findOrFail($id);

        return $this->entity::fromArray($data->toArray(), ['roles']);
    }
}
