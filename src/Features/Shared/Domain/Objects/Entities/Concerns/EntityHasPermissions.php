<?php

namespace Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\Concerns;

use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Attributes\RelationOf;
use Thehouseofel\Kalion\Core\Domain\Services\Auth\UserAccessChecker;
use Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories\PermissionRepository;
use Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories\RoleRepository;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\Collections\RoleCollection;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\RoleEntity;

trait EntityHasPermissions
{
    protected UserAccessChecker $accessChecker;
    protected RoleRepository $repositoryRole;
    protected PermissionRepository $repositoryPermission;

    protected array $is  = [];
    protected array $can = [];

    protected function accessChecker(): UserAccessChecker
    {
        return $this->accessChecker ??= app(UserAccessChecker::class);
    }

    protected function repositoryRole(): RoleRepository
    {
        return $this->repositoryRole ??= app(RoleRepository::class);
    }

    protected function repositoryPermission(): PermissionRepository
    {
        return $this->repositoryPermission ??= app(PermissionRepository::class);
    }

    public function can(string|array $permission, ...$params): bool
    {
        return $this->accessChecker()->can($this, $permission, $params);
    }

    public function is(string|array $role, ...$params): bool
    {
        return $this->accessChecker()->is($this, $role, $params);
    }

    public function setIs(): void
    {
        $is = [];
        foreach ($this->repositoryRole()->all() as $role) {
            $roleName = $role->name->value;
            $is[$roleName] = $this->is($roleName);
        }
        $this->is = $is;
    }

    public function setCan(): void
    {
        $can = [];
        foreach ($this->repositoryPermission()->all() as $permission) {
            $permissionName = $permission->name->value;
            $can[$permissionName] = $this->can($permissionName);
        }
        $this->can = $can;
    }

    public function toArray($addPermissions = false, $addRoles = false): array
    {
        if ($addPermissions) {
            $this->setCan();
        }

        if ($addRoles) {
            $this->setIs();
        }

        $base = parent::toArray();
        $base['is'] = $this->is;
        $base['can'] = $this->can;
        return $base;
    }

    /*----------------------------------------------------------------------------------------------------------------*/
    /*--------------------------------------------------- Relations -------------------------------------------------*/

    #[RelationOf(RoleCollection::class)]
    public function roles(): RoleCollection
    {
        return $this->getRelation();
    }


    /*----------------------------------------------------------------------------------------------------------------*/
    /*--------------------------------------------------- Properties -------------------------------------------------*/

    public function all_permissions(): bool
    {
        return $this->roles()->contains(fn(RoleEntity $role) => $role->all_permissions->isTrue());
    }
}
